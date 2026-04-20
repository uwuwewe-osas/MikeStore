<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

$page = (string) ($_GET['page'] ?? 'dashboard');
$allowedPages = ['login', 'dashboard', 'pos', 'products', 'inventory', 'customers', 'sales', 'cash', 'reports'];

if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

if (is_post()) {
    verify_csrf();
    keep_old($_POST);

    try {
        $action = (string) ($_POST['action'] ?? '');

        switch ($action) {
            case 'login':
                if (attempt_login(trim((string) $_POST['email']), (string) $_POST['password'])) {
                    clear_old();
                    flash('success', 'Sesion iniciada correctamente.');
                    redirect_to('/?page=dashboard');
                }

                flash('error', 'Credenciales invalidas.');
                redirect_to('/?page=login');

            case 'logout':
                logout_user();
                flash('success', 'Sesion cerrada.');
                redirect_to('/?page=login');

            case 'create_product':
                require_auth();
                require_role(['admin', 'manager']);
                create_product($_POST);
                clear_old();
                flash('success', 'Producto registrado correctamente.');
                redirect_to('/?page=products');

            case 'adjust_inventory':
                require_auth();
                require_role(['admin', 'manager']);
                adjust_inventory($_POST);
                clear_old();
                flash('success', 'Inventario actualizado.');
                redirect_to('/?page=inventory');

            case 'create_customer':
                require_auth();
                create_customer($_POST);
                clear_old();
                flash('success', 'Cliente registrado.');
                redirect_to('/?page=customers');

            case 'open_cash':
                require_auth();
                open_cash_session($_POST);
                clear_old();
                flash('success', 'Caja abierta correctamente.');
                redirect_to('/?page=cash');

            case 'close_cash':
                require_auth();
                close_cash_session($_POST);
                clear_old();
                flash('success', 'Caja cerrada correctamente.');
                redirect_to('/?page=cash');

            case 'create_sale':
                require_auth();
                $saleNumber = create_sale($_POST);
                clear_old();
                flash('success', 'Venta registrada: ' . $saleNumber);
                redirect_to('/?page=sales');

            default:
                flash('error', 'Accion no reconocida.');
                redirect_to('/?page=' . $page);
        }
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect_to('/?page=' . $page);
    }
}

if ($page !== 'login') {
    require_auth();
}

$user = current_user();
$flashes = get_flashes();

if ($page === 'login') {
    if ($user) {
        redirect_to('/?page=dashboard');
    }
    require dirname(__DIR__) . '/views/login.php';
    exit;
}

$data = match ($page) {
    'dashboard' => ['metrics' => dashboard_metrics()],
    'products' => ['products' => list_products()],
    'inventory' => ['variants' => list_variants()],
    'customers' => ['customers' => list_customers()],
    'sales' => ['sales' => list_sales(), 'saleDetail' => isset($_GET['sale_id']) ? get_sale_detail((int) $_GET['sale_id']) : null],
    'cash' => ['sessions' => list_cash_sessions(), 'openCash' => get_open_cash_session((int) $user['id'])],
    'reports' => ['report' => sales_report_summary()],
    'pos' => ['variants' => list_variants(), 'customers' => list_customers(), 'openCash' => get_open_cash_session((int) $user['id'])],
    default => [],
};

require dirname(__DIR__) . '/views/layout.php';
