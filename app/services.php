<?php

declare(strict_types=1);

function dashboard_metrics(): array
{
    $todaySales = fetch_one(
        "SELECT COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS revenue
         FROM sales
         WHERE DATE(created_at) = CURDATE() AND sale_status = 'completed'"
    ) ?: ['total_sales' => 0, 'revenue' => 0];

    $lowStock = fetch_one(
        'SELECT COUNT(*) AS total FROM product_variants WHERE stock <= min_stock'
    ) ?: ['total' => 0];

    $openCash = fetch_one(
        "SELECT COUNT(*) AS total FROM cash_sessions WHERE status = 'open'"
    ) ?: ['total' => 0];

    $topProducts = fetch_all(
        "SELECT si.product_name, SUM(si.quantity) AS units, SUM(si.line_total) AS revenue
         FROM sale_items si
         INNER JOIN sales s ON s.id = si.sale_id
         WHERE s.sale_status = 'completed'
         GROUP BY si.product_name
         ORDER BY units DESC, revenue DESC
         LIMIT 5"
    );

    return [
        'today_sales' => (int) $todaySales['total_sales'],
        'today_revenue' => (float) $todaySales['revenue'],
        'low_stock' => (int) $lowStock['total'],
        'open_cash_sessions' => (int) $openCash['total'],
        'top_products' => $topProducts,
    ];
}

function list_products(): array
{
    return fetch_all(
        "SELECT p.*, COALESCE(SUM(v.stock), 0) AS total_stock, COUNT(v.id) AS variants_count
         FROM products p
         LEFT JOIN product_variants v ON v.product_id = p.id
         GROUP BY p.id
         ORDER BY p.created_at DESC"
    );
}

function list_variants(): array
{
    return fetch_all(
        "SELECT v.*, p.name AS product_name, p.sku, p.price, p.brand
         FROM product_variants v
         INNER JOIN products p ON p.id = v.product_id
         ORDER BY p.name ASC, v.size_label ASC"
    );
}

function list_customers(): array
{
    return fetch_all('SELECT * FROM customers ORDER BY created_at DESC');
}

function list_sales(): array
{
    return fetch_all(
        "SELECT s.*, c.full_name AS customer_name, u.name AS user_name
         FROM sales s
         LEFT JOIN customers c ON c.id = s.customer_id
         INNER JOIN users u ON u.id = s.user_id
         ORDER BY s.created_at DESC
         LIMIT 100"
    );
}

function get_sale_detail(int $saleId): ?array
{
    $sale = fetch_one(
        "SELECT s.*, c.full_name AS customer_name, c.document_number, u.name AS user_name
         FROM sales s
         LEFT JOIN customers c ON c.id = s.customer_id
         INNER JOIN users u ON u.id = s.user_id
         WHERE s.id = ?",
        [$saleId]
    );

    if (!$sale) {
        return null;
    }

    $sale['items'] = fetch_all(
        'SELECT * FROM sale_items WHERE sale_id = ? ORDER BY id ASC',
        [$saleId]
    );

    return $sale;
}

function list_cash_sessions(): array
{
    return fetch_all(
        "SELECT cs.*, u.name AS user_name
         FROM cash_sessions cs
         INNER JOIN users u ON u.id = cs.user_id
         ORDER BY cs.opened_at DESC"
    );
}

function get_open_cash_session(int $userId): ?array
{
    return fetch_one(
        "SELECT * FROM cash_sessions WHERE user_id = ? AND status = 'open' ORDER BY opened_at DESC LIMIT 1",
        [$userId]
    );
}

function sales_report_summary(): array
{
    $summary = fetch_one(
        "SELECT
            COUNT(*) AS total_sales,
            COALESCE(SUM(total), 0) AS total_revenue,
            COALESCE(SUM(discount), 0) AS total_discount,
            COALESCE(SUM(tax), 0) AS total_tax
         FROM sales
         WHERE sale_status = 'completed'"
    ) ?: [];

    $paymentMethods = fetch_all(
        "SELECT payment_method, COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS amount
         FROM sales
         WHERE sale_status = 'completed'
         GROUP BY payment_method
         ORDER BY amount DESC"
    );

    $cashBalance = fetch_one(
        "SELECT COALESCE(SUM(
            CASE
                WHEN movement_type IN ('opening', 'sale', 'income') THEN amount
                WHEN movement_type IN ('expense', 'closing') THEN amount * -1
                ELSE 0
            END
        ), 0) AS balance
         FROM cash_movements"
    ) ?: ['balance' => 0];

    return [
        'summary' => $summary,
        'payment_methods' => $paymentMethods,
        'cash_balance' => (float) $cashBalance['balance'],
    ];
}

function create_product(array $data): void
{
    execute_query(
        'INSERT INTO products (sku, name, brand, category, gender, description, cost, price, is_active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
            trim($data['sku']),
            trim($data['name']),
            trim($data['brand']),
            trim($data['category']),
            trim($data['gender']),
            trim($data['description'] ?? ''),
            (float) $data['cost'],
            (float) $data['price'],
            isset($data['is_active']) ? 1 : 0,
        ]
    );

    $productId = (int) db()->lastInsertId();
    create_variant($productId, $data);
}

function create_variant(int $productId, array $data): void
{
    execute_query(
        'INSERT INTO product_variants (product_id, size_label, barcode, stock, min_stock)
         VALUES (?, ?, ?, ?, ?)',
        [
            $productId,
            trim($data['size_label']),
            trim($data['barcode'] ?: '') ?: null,
            (int) $data['stock'],
            (int) $data['min_stock'],
        ]
    );

    $variantId = (int) db()->lastInsertId();
    execute_query(
        'INSERT INTO inventory_movements (variant_id, movement_type, quantity, note, user_id)
         VALUES (?, ?, ?, ?, ?)',
        [
            $variantId,
            'purchase',
            (int) $data['stock'],
            'Stock inicial del producto',
            current_user()['id'] ?? null,
        ]
    );
}

function adjust_inventory(array $data): void
{
    $variant = fetch_one('SELECT * FROM product_variants WHERE id = ?', [(int) $data['variant_id']]);
    if (!$variant) {
        throw new RuntimeException('Variante no encontrada.');
    }

    $newStock = max(0, (int) $variant['stock'] + (int) $data['quantity']);
    execute_query('UPDATE product_variants SET stock = ? WHERE id = ?', [$newStock, $variant['id']]);
    execute_query(
        'INSERT INTO inventory_movements (variant_id, movement_type, quantity, note, user_id)
         VALUES (?, ?, ?, ?, ?)',
        [
            $variant['id'],
            'adjustment',
            (int) $data['quantity'],
            trim($data['note']),
            current_user()['id'] ?? null,
        ]
    );
}

function create_customer(array $data): void
{
    execute_query(
        'INSERT INTO customers (full_name, document_type, document_number, email, phone, address, loyalty_points)
         VALUES (?, ?, ?, ?, ?, ?, ?)',
        [
            trim($data['full_name']),
            trim($data['document_type']),
            trim($data['document_number']),
            trim($data['email'] ?: '') ?: null,
            trim($data['phone'] ?: '') ?: null,
            trim($data['address'] ?: '') ?: null,
            (int) ($data['loyalty_points'] ?? 0),
        ]
    );
}

function open_cash_session(array $data): void
{
    $user = current_user();
    if (!$user) {
        throw new RuntimeException('Usuario no autenticado.');
    }

    if (get_open_cash_session((int) $user['id'])) {
        throw new RuntimeException('Ya existe una caja abierta para este usuario.');
    }

    execute_query(
        'INSERT INTO cash_sessions (user_id, opening_amount, status) VALUES (?, ?, ?)',
        [$user['id'], (float) $data['opening_amount'], 'open']
    );

    $sessionId = (int) db()->lastInsertId();
    execute_query(
        'INSERT INTO cash_movements (cash_session_id, movement_type, payment_method, amount, note)
         VALUES (?, ?, ?, ?, ?)',
        [$sessionId, 'opening', 'cash', (float) $data['opening_amount'], 'Apertura de caja']
    );
}

function close_cash_session(array $data): void
{
    $session = fetch_one('SELECT * FROM cash_sessions WHERE id = ? AND status = ?', [(int) $data['cash_session_id'], 'open']);
    if (!$session) {
        throw new RuntimeException('Caja abierta no encontrada.');
    }

    $expected = fetch_one(
        "SELECT COALESCE(SUM(
            CASE
                WHEN movement_type IN ('opening', 'sale', 'income') THEN amount
                WHEN movement_type IN ('expense', 'closing') THEN amount * -1
                ELSE 0
            END
        ), 0) AS expected_total
         FROM cash_movements
         WHERE cash_session_id = ?",
        [$session['id']]
    );

    $closingAmount = (float) $data['closing_amount'];
    $expectedAmount = (float) ($expected['expected_total'] ?? 0);
    $difference = $closingAmount - $expectedAmount;

    execute_query(
        'UPDATE cash_sessions
         SET closing_amount = ?, expected_amount = ?, difference_amount = ?, status = ?, closed_at = NOW()
         WHERE id = ?',
        [$closingAmount, $expectedAmount, $difference, 'closed', $session['id']]
    );

    execute_query(
        'INSERT INTO cash_movements (cash_session_id, movement_type, payment_method, amount, note)
         VALUES (?, ?, ?, ?, ?)',
        [$session['id'], 'closing', 'cash', $closingAmount, trim($data['note'] ?: 'Cierre de caja')]
    );
}

function create_sale(array $data): string
{
    $user = current_user();
    if (!$user) {
        throw new RuntimeException('Usuario no autenticado.');
    }

    $cashSession = get_open_cash_session((int) $user['id']);
    if (($data['payment_method'] ?? 'cash') === 'cash' && !$cashSession) {
        throw new RuntimeException('Debes abrir caja antes de registrar ventas en efectivo.');
    }

    $items = json_decode($data['items_json'] ?? '[]', true);
    if (!is_array($items) || $items === []) {
        throw new RuntimeException('No hay items en la venta.');
    }

    db()->beginTransaction();

    try {
        $subtotal = 0.0;
        $normalizedItems = [];

        foreach ($items as $item) {
            $variantId = (int) ($item['variant_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($variantId <= 0 || $quantity <= 0) {
                throw new RuntimeException('Datos de item invalidos.');
            }

            $variant = fetch_one(
                "SELECT v.*, p.id AS product_id, p.name, p.price
                 FROM product_variants v
                 INNER JOIN products p ON p.id = v.product_id
                 WHERE v.id = ? FOR UPDATE",
                [$variantId]
            );

            if (!$variant) {
                throw new RuntimeException('Variante no encontrada.');
            }

            if ((int) $variant['stock'] < $quantity) {
                throw new RuntimeException('Stock insuficiente para ' . $variant['name'] . ' talla ' . $variant['size_label'] . '.');
            }

            $lineTotal = (float) $variant['price'] * $quantity;
            $subtotal += $lineTotal;
            $normalizedItems[] = [
                'variant' => $variant,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        $discount = (float) ($data['discount'] ?? 0);
        $taxRate = (float) ($data['tax_rate'] ?? 0);
        $tax = max(0, (($subtotal - $discount) * $taxRate) / 100);
        $total = max(0, $subtotal - $discount + $tax);
        $saleNumber = 'VTA-' . date('YmdHis') . '-' . random_int(100, 999);

        execute_query(
            'INSERT INTO sales (sale_number, customer_id, user_id, cash_session_id, subtotal, discount, tax, total, payment_method, sale_status, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $saleNumber,
                (int) ($data['customer_id'] ?: 0) ?: null,
                $user['id'],
                $cashSession['id'] ?? null,
                $subtotal,
                $discount,
                $tax,
                $total,
                trim($data['payment_method'] ?? 'cash'),
                'completed',
                trim($data['notes'] ?? '') ?: null,
            ]
        );

        $saleId = (int) db()->lastInsertId();

        foreach ($normalizedItems as $item) {
            $variant = $item['variant'];
            $newStock = (int) $variant['stock'] - $item['quantity'];

            execute_query(
                'INSERT INTO sale_items (sale_id, product_id, variant_id, product_name, size_label, quantity, unit_price, line_total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $saleId,
                    $variant['product_id'],
                    $variant['id'],
                    $variant['name'],
                    $variant['size_label'],
                    $item['quantity'],
                    $variant['price'],
                    $item['line_total'],
                ]
            );

            execute_query('UPDATE product_variants SET stock = ? WHERE id = ?', [$newStock, $variant['id']]);

            execute_query(
                'INSERT INTO inventory_movements (variant_id, movement_type, quantity, note, user_id)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $variant['id'],
                    'sale',
                    -1 * $item['quantity'],
                    'Venta ' . $saleNumber,
                    $user['id'],
                ]
            );
        }

        if (($data['payment_method'] ?? 'cash') === 'cash' && $cashSession) {
            execute_query(
                'INSERT INTO cash_movements (cash_session_id, movement_type, payment_method, amount, note)
                 VALUES (?, ?, ?, ?, ?)',
                [$cashSession['id'], 'sale', 'cash', $total, 'Venta ' . $saleNumber]
            );
        }

        db()->commit();
        return $saleNumber;
    } catch (Throwable $exception) {
        db()->rollBack();
        throw $exception;
    }
}
