<?php
declare(strict_types=1);

$pageTitles = [
    'dashboard' => ['Dashboard', 'Vista general del negocio en tiempo real.'],
    'pos' => ['Caja POS', 'Registra ventas rapidas y descuenta inventario automaticamente.'],
    'products' => ['Productos', 'Catalogo principal con SKU, costos y precios.'],
    'inventory' => ['Inventario', 'Control por talla, stock minimo y ajustes manuales.'],
    'customers' => ['Clientes', 'Base de clientes con documento, contacto e historial.'],
    'sales' => ['Ventas', 'Historial de tickets y detalle de cada transaccion.'],
    'cash' => ['Caja', 'Apertura, cierre y movimientos de caja por usuario.'],
    'reports' => ['Reportes', 'Metricas de ventas, medios de pago y caja.'],
];

[$title, $subtitle] = $pageTitles[$page] ?? ['MikeZapatillas POS', ''];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title) ?> | <?= h(app_config()['name']) ?></title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="/?page=dashboard">MikeZapatillas POS</a>
            <span class="brand-subtitle">Ventas, inventario y control operativo</span>

            <nav>
                <a class="nav-link <?= nav_is_active('dashboard', $page) ?>" href="/?page=dashboard">Dashboard <span>01</span></a>
                <a class="nav-link <?= nav_is_active('pos', $page) ?>" href="/?page=pos">Caja POS <span>02</span></a>
                <a class="nav-link <?= nav_is_active('products', $page) ?>" href="/?page=products">Productos <span>03</span></a>
                <a class="nav-link <?= nav_is_active('inventory', $page) ?>" href="/?page=inventory">Inventario <span>04</span></a>
                <a class="nav-link <?= nav_is_active('customers', $page) ?>" href="/?page=customers">Clientes <span>05</span></a>
                <a class="nav-link <?= nav_is_active('sales', $page) ?>" href="/?page=sales">Ventas <span>06</span></a>
                <a class="nav-link <?= nav_is_active('cash', $page) ?>" href="/?page=cash">Caja <span>07</span></a>
                <a class="nav-link <?= nav_is_active('reports', $page) ?>" href="/?page=reports">Reportes <span>08</span></a>
            </nav>

            <div class="sidebar-footer">
                <div class="small muted">Activo como</div>
                <div style="margin:6px 0 14px;font-weight:700;"><?= h($user['name']) ?></div>
                <span class="<?= badge_class($user['role']) ?>"><?= h($user['role']) ?></span>
                <form method="post" action="/?page=dashboard" style="margin-top:16px;">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="logout">
                    <button class="btn btn-secondary btn-block" type="submit">Cerrar sesion</button>
                </form>
            </div>
        </aside>

        <main class="content">
            <div class="topbar">
                <div class="page-title">
                    <h1><?= h($title) ?></h1>
                    <p><?= h($subtitle) ?></p>
                </div>
                <div class="user-card">
                    <div>
                        <strong><?= h($user['name']) ?></strong>
                        <div class="muted small"><?= h($user['email']) ?></div>
                    </div>
                    <span class="<?= badge_class($user['role']) ?>"><?= h($user['role']) ?></span>
                </div>
            </div>

            <?php if ($flashes): ?>
                <div class="flash-wrap">
                    <?php foreach ($flashes as $flash): ?>
                        <div class="flash <?= $flash['type'] === 'success' ? 'flash-success' : 'flash-error' ?>">
                            <?= h($flash['message']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php require __DIR__ . '/pages/' . $page . '.php'; ?>
        </main>
    </div>
</body>
</html>
