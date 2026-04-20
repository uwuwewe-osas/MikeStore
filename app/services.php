<?php

declare(strict_types=1);

function gender_label(?string $value): string
{
    return match ($value) {
        'hombre' => 'Hombre',
        'mujeres' => 'Mujer',
        'ninos' => 'Ninos',
        default => ucfirst((string) $value),
    };
}

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

    $channelSummary = fetch_all(
        "SELECT sales_channel, COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS amount
         FROM sales
         WHERE sale_status = 'completed'
         GROUP BY sales_channel
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
        'channels' => $channelSummary,
        'cash_balance' => (float) $cashBalance['balance'],
    ];
}

function create_product(array $data): void
{
    $slug = trim((string) ($data['slug'] ?? ''));
    if ($slug === '') {
        $slug = slugify(trim((string) $data['name']));
    }

    execute_query(
        'INSERT INTO products (sku, slug, name, brand, category, gender, description, image_url, accent_color, featured, cost, price, is_active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
            trim((string) $data['sku']),
            $slug,
            trim((string) $data['name']),
            trim((string) $data['brand']),
            trim((string) $data['category']),
            trim((string) $data['gender']),
            trim((string) ($data['description'] ?? '')),
            trim((string) ($data['image_url'] ?? '')) ?: null,
            trim((string) ($data['accent_color'] ?? '')) ?: '#0f766e',
            isset($data['featured']) ? 1 : 0,
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
            trim((string) $data['size_label']),
            trim((string) ($data['barcode'] ?? '')) ?: null,
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
            trim((string) $data['note']),
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
            trim((string) $data['full_name']),
            trim((string) $data['document_type']),
            trim((string) $data['document_number']),
            trim((string) ($data['email'] ?? '')) ?: null,
            trim((string) ($data['phone'] ?? '')) ?: null,
            trim((string) ($data['address'] ?? '')) ?: null,
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
        [$session['id'], 'closing', 'cash', $closingAmount, trim((string) ($data['note'] ?? 'Cierre de caja'))]
    );
}

function create_sale(array $data): string
{
    $user = current_user();
    if (!$user) {
        throw new RuntimeException('Usuario no autenticado.');
    }

    $items = json_decode((string) ($data['items_json'] ?? '[]'), true);
    if (!is_array($items) || $items === []) {
        throw new RuntimeException('No hay items en la venta.');
    }

    $cashSession = get_open_cash_session((int) $user['id']);
    if (($data['payment_method'] ?? 'cash') === 'cash' && !$cashSession) {
        throw new RuntimeException('Debes abrir caja antes de registrar ventas en efectivo.');
    }

    return persist_sale([
        'user_id' => (int) $user['id'],
        'customer_id' => (int) ($data['customer_id'] ?: 0) ?: null,
        'cash_session_id' => $cashSession['id'] ?? null,
        'items' => $items,
        'discount' => (float) ($data['discount'] ?? 0),
        'tax_rate' => (float) ($data['tax_rate'] ?? 0),
        'payment_method' => trim((string) ($data['payment_method'] ?? 'cash')),
        'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
        'sales_channel' => 'pos',
        'sale_prefix' => 'VTA',
    ]);
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? $text;
    return trim($text, '-') ?: 'producto';
}

function storefront_home_data(): array
{
    return [
        'featured' => fetch_all(
            "SELECT p.*, COALESCE(SUM(v.stock), 0) AS total_stock, MIN(v.size_label) AS first_size
             FROM products p
             LEFT JOIN product_variants v ON v.product_id = p.id
             WHERE p.is_active = 1 AND p.featured = 1
             GROUP BY p.id
             ORDER BY p.created_at DESC
             LIMIT 6"
        ),
        'categories' => fetch_all(
            "SELECT category, COUNT(*) AS total_products
             FROM products
             WHERE is_active = 1
             GROUP BY category
             ORDER BY total_products DESC, category ASC"
        ),
        'new_arrivals' => fetch_all(
            "SELECT p.*, COALESCE(SUM(v.stock), 0) AS total_stock
             FROM products p
             LEFT JOIN product_variants v ON v.product_id = p.id
             WHERE p.is_active = 1
             GROUP BY p.id
             ORDER BY p.created_at DESC
             LIMIT 8"
        ),
    ];
}

function storefront_products(array $filters = []): array
{
    $sql = "SELECT p.*, COALESCE(SUM(v.stock), 0) AS total_stock
            FROM products p
            LEFT JOIN product_variants v ON v.product_id = p.id
            WHERE p.is_active = 1";
    $params = [];

    if (!empty($filters['category'])) {
        $sql .= " AND p.category = ?";
        $params[] = $filters['category'];
    }

    if (!empty($filters['gender'])) {
        $sql .= " AND p.gender = ?";
        $params[] = $filters['gender'];
    }

    if (!empty($filters['q'])) {
        $sql .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.category LIKE ?)";
        $needle = '%' . $filters['q'] . '%';
        $params[] = $needle;
        $params[] = $needle;
        $params[] = $needle;
    }

    $sql .= " GROUP BY p.id";

    $sort = $filters['sort'] ?? 'featured';
    $sql .= match ($sort) {
        'price_asc' => " ORDER BY p.price ASC",
        'price_desc' => " ORDER BY p.price DESC",
        'name' => " ORDER BY p.name ASC",
        default => " ORDER BY p.featured DESC, p.created_at DESC",
    };

    return fetch_all($sql, $params);
}

function storefront_filters(): array
{
    return [
        'categories' => fetch_all("SELECT category, COUNT(*) AS total FROM products WHERE is_active = 1 GROUP BY category ORDER BY category ASC"),
        'genders' => fetch_all("SELECT gender, COUNT(*) AS total FROM products WHERE is_active = 1 GROUP BY gender ORDER BY gender ASC"),
    ];
}

function storefront_product(string $slug): ?array
{
    $product = fetch_one(
        "SELECT p.*, COALESCE(SUM(v.stock), 0) AS total_stock
         FROM products p
         LEFT JOIN product_variants v ON v.product_id = p.id
         WHERE p.slug = ? AND p.is_active = 1
         GROUP BY p.id",
        [$slug]
    );

    if (!$product) {
        return null;
    }

    $product['variants'] = fetch_all(
        "SELECT * FROM product_variants WHERE product_id = ? ORDER BY CAST(size_label AS DECIMAL(10,2)) ASC, size_label ASC",
        [$product['id']]
    );

    $product['related'] = fetch_all(
        "SELECT p.*, COALESCE(SUM(v.stock), 0) AS total_stock
         FROM products p
         LEFT JOIN product_variants v ON v.product_id = p.id
         WHERE p.id <> ? AND p.is_active = 1 AND (p.category = ? OR p.gender = ?)
         GROUP BY p.id
         ORDER BY p.featured DESC, p.created_at DESC
         LIMIT 4",
        [$product['id'], $product['category'], $product['gender']]
    );

    return $product;
}

function storefront_cart(): array
{
    $rawCart = $_SESSION['store_cart'] ?? [];
    if (!is_array($rawCart) || $rawCart === []) {
        return [
            'items' => [],
            'count' => 0,
            'subtotal' => 0.0,
        ];
    }

    $items = [];
    $count = 0;
    $subtotal = 0.0;

    foreach ($rawCart as $variantId => $quantity) {
        $variantId = (int) $variantId;
        $quantity = (int) $quantity;
        if ($variantId <= 0 || $quantity <= 0) {
            continue;
        }

        $variant = fetch_one(
            "SELECT v.id AS variant_id, v.size_label, v.stock, p.id AS product_id, p.slug, p.name, p.brand, p.price, p.image_url
             FROM product_variants v
             INNER JOIN products p ON p.id = v.product_id
             WHERE v.id = ? AND p.is_active = 1",
            [$variantId]
        );

        if (!$variant) {
            continue;
        }

        $quantity = min($quantity, (int) $variant['stock']);
        if ($quantity <= 0) {
            continue;
        }

        $lineTotal = (float) $variant['price'] * $quantity;
        $count += $quantity;
        $subtotal += $lineTotal;
        $items[] = [
            'variant_id' => $variant['variant_id'],
            'product_id' => $variant['product_id'],
            'slug' => $variant['slug'],
            'name' => $variant['name'],
            'brand' => $variant['brand'],
            'image_url' => $variant['image_url'],
            'size_label' => $variant['size_label'],
            'price' => (float) $variant['price'],
            'quantity' => $quantity,
            'stock' => (int) $variant['stock'],
            'line_total' => $lineTotal,
        ];
    }

    $_SESSION['store_cart'] = array_column($items, 'quantity', 'variant_id');

    return [
        'items' => $items,
        'count' => $count,
        'subtotal' => $subtotal,
    ];
}

function add_to_storefront_cart(int $variantId, int $quantity = 1): void
{
    $variant = fetch_one(
        "SELECT v.id, v.stock, p.name, p.slug
         FROM product_variants v
         INNER JOIN products p ON p.id = v.product_id
         WHERE v.id = ? AND p.is_active = 1",
        [$variantId]
    );

    if (!$variant) {
        throw new RuntimeException('La talla seleccionada no existe.');
    }

    if ((int) $variant['stock'] <= 0) {
        throw new RuntimeException('Esa talla ya no tiene stock.');
    }

    $cart = $_SESSION['store_cart'] ?? [];
    $current = (int) ($cart[$variantId] ?? 0);
    $next = min($current + max(1, $quantity), (int) $variant['stock']);
    $cart[$variantId] = $next;
    $_SESSION['store_cart'] = $cart;
}

function update_storefront_cart(array $quantities): void
{
    $nextCart = $_SESSION['store_cart'] ?? [];

    foreach ($quantities as $variantId => $quantity) {
        $variantId = (int) $variantId;
        $quantity = (int) $quantity;
        if ($variantId <= 0) {
            continue;
        }

        if ($quantity <= 0) {
            unset($nextCart[$variantId]);
            continue;
        }

        $stockRow = fetch_one('SELECT stock FROM product_variants WHERE id = ?', [$variantId]);
        if (!$stockRow || (int) $stockRow['stock'] <= 0) {
            unset($nextCart[$variantId]);
            continue;
        }

        $nextCart[$variantId] = min($quantity, (int) $stockRow['stock']);
    }

    $_SESSION['store_cart'] = $nextCart;
}

function remove_from_storefront_cart(int $variantId): void
{
    $cart = $_SESSION['store_cart'] ?? [];
    unset($cart[$variantId]);
    $_SESSION['store_cart'] = $cart;
}

function clear_storefront_cart(): void
{
    unset($_SESSION['store_cart']);
}

function storefront_cart_count(): int
{
    return storefront_cart()['count'];
}

function storefront_featured_categories(): array
{
    return fetch_all(
        "SELECT category, COUNT(*) AS total_products, MAX(accent_color) AS accent_color
         FROM products
         WHERE is_active = 1
         GROUP BY category
         ORDER BY total_products DESC, category ASC"
    );
}

function find_or_create_online_customer(array $data): int
{
    $email = trim((string) ($data['email'] ?? ''));
    $documentType = trim((string) ($data['document_type'] ?? 'DNI'));
    $documentNumber = trim((string) ($data['document_number'] ?? ''));

    if ($email !== '') {
        $customer = fetch_one('SELECT id FROM customers WHERE email = ? LIMIT 1', [$email]);
        if ($customer) {
            execute_query(
                'UPDATE customers SET full_name = ?, phone = ?, address = ?, document_type = ?, document_number = ? WHERE id = ?',
                [
                    trim((string) $data['full_name']),
                    trim((string) ($data['phone'] ?? '')) ?: null,
                    trim((string) ($data['address'] ?? '')) ?: null,
                    $documentType,
                    $documentNumber,
                    $customer['id'],
                ]
            );
            return (int) $customer['id'];
        }
    }

    $customer = fetch_one(
        'SELECT id FROM customers WHERE document_type = ? AND document_number = ? LIMIT 1',
        [$documentType, $documentNumber]
    );

    if ($customer) {
        execute_query(
            'UPDATE customers SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?',
            [
                trim((string) $data['full_name']),
                $email ?: null,
                trim((string) ($data['phone'] ?? '')) ?: null,
                trim((string) ($data['address'] ?? '')) ?: null,
                $customer['id'],
            ]
        );
        return (int) $customer['id'];
    }

    execute_query(
        'INSERT INTO customers (full_name, document_type, document_number, email, phone, address, loyalty_points)
         VALUES (?, ?, ?, ?, ?, ?, 0)',
        [
            trim((string) $data['full_name']),
            $documentType,
            $documentNumber,
            $email ?: null,
            trim((string) ($data['phone'] ?? '')) ?: null,
            trim((string) ($data['address'] ?? '')) ?: null,
        ]
    );

    return (int) db()->lastInsertId();
}

function online_sales_user_id(): int
{
    $user = fetch_one("SELECT id FROM users WHERE email = 'store@mikepos.local' LIMIT 1");
    return (int) ($user['id'] ?? 1);
}

function persist_sale(array $payload): string
{
    $items = $payload['items'] ?? [];
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

        $discount = (float) ($payload['discount'] ?? 0);
        $taxRate = (float) ($payload['tax_rate'] ?? 0);
        $tax = max(0, (($subtotal - $discount) * $taxRate) / 100);
        $total = max(0, $subtotal - $discount + $tax);
        $prefix = trim((string) ($payload['sale_prefix'] ?? 'VTA'));
        $saleNumber = $prefix . '-' . date('YmdHis') . '-' . random_int(100, 999);

        execute_query(
            'INSERT INTO sales (sale_number, customer_id, user_id, cash_session_id, subtotal, discount, tax, total, payment_method, sales_channel, sale_status, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $saleNumber,
                $payload['customer_id'] ?? null,
                (int) $payload['user_id'],
                $payload['cash_session_id'] ?? null,
                $subtotal,
                $discount,
                $tax,
                $total,
                trim((string) ($payload['payment_method'] ?? 'cash')),
                trim((string) ($payload['sales_channel'] ?? 'pos')),
                'completed',
                $payload['notes'] ?? null,
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
                    (int) $payload['user_id'],
                ]
            );
        }

        if (($payload['payment_method'] ?? 'cash') === 'cash' && !empty($payload['cash_session_id'])) {
            execute_query(
                'INSERT INTO cash_movements (cash_session_id, movement_type, payment_method, amount, note)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $payload['cash_session_id'],
                    'sale',
                    'cash',
                    $total,
                    'Venta ' . $saleNumber,
                ]
            );
        }

        db()->commit();
        return $saleNumber;
    } catch (Throwable $exception) {
        db()->rollBack();
        throw $exception;
    }
}

function create_online_order(array $data): string
{
    $cart = storefront_cart();
    if ($cart['items'] === []) {
        throw new RuntimeException('Tu carrito esta vacio.');
    }

    $required = ['full_name', 'email', 'phone', 'address', 'document_number'];
    foreach ($required as $field) {
        if (trim((string) ($data[$field] ?? '')) === '') {
            throw new RuntimeException('Completa todos los datos del checkout.');
        }
    }

    $customerId = find_or_create_online_customer($data);
    $saleNumber = persist_sale([
        'user_id' => online_sales_user_id(),
        'customer_id' => $customerId,
        'cash_session_id' => null,
        'items' => array_map(
            static fn(array $item): array => ['variant_id' => $item['variant_id'], 'quantity' => $item['quantity']],
            $cart['items']
        ),
        'discount' => 0.0,
        'tax_rate' => 18.0,
        'payment_method' => trim((string) ($data['payment_method'] ?? 'card')),
        'notes' => 'Pedido web para despacho a ' . trim((string) $data['address']),
        'sales_channel' => 'online',
        'sale_prefix' => 'WEB',
    ]);

    clear_storefront_cart();
    $_SESSION['last_order_number'] = $saleNumber;

    return $saleNumber;
}
