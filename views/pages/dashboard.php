<?php $metrics = $data['metrics']; ?>
<section class="grid cols-4">
    <article class="card stat-card">
        <div class="stat-label">Ventas de hoy</div>
        <div class="stat-value"><?= h((string) $metrics['today_sales']) ?></div>
    </article>
    <article class="card stat-card">
        <div class="stat-label">Ingresos de hoy</div>
        <div class="stat-value"><?= h(currency($metrics['today_revenue'])) ?></div>
    </article>
    <article class="card stat-card">
        <div class="stat-label">Alertas de stock</div>
        <div class="stat-value"><?= h((string) $metrics['low_stock']) ?></div>
    </article>
    <article class="card stat-card">
        <div class="stat-label">Cajas abiertas</div>
        <div class="stat-value"><?= h((string) $metrics['open_cash_sessions']) ?></div>
    </article>
</section>

<section class="grid cols-2" style="margin-top:20px;">
    <article class="card">
        <div class="toolbar">
            <h2>Top productos</h2>
            <a class="btn btn-secondary" href="/?page=reports">Ver reportes</a>
        </div>
        <?php if (!$metrics['top_products']): ?>
            <div class="empty-state">Aun no hay ventas registradas.</div>
        <?php else: ?>
            <ul class="kpi-list">
                <?php foreach ($metrics['top_products'] as $product): ?>
                    <li>
                        <span><?= h($product['product_name']) ?></span>
                        <strong><?= h((string) $product['units']) ?> und · <?= h(currency($product['revenue'])) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Que ya resuelve este MVP</h2>
        <ul class="kpi-list">
            <li><span>Autenticacion segura</span><strong>Usuarios + roles</strong></li>
            <li><span>Venta en caja</span><strong>Descuenta stock</strong></li>
            <li><span>Clientes</span><strong>Registro base</strong></li>
            <li><span>Inventario</span><strong>Por talla</strong></li>
            <li><span>Caja</span><strong>Apertura y cierre</strong></li>
            <li><span>Reportes</span><strong>Ventas y pagos</strong></li>
        </ul>
    </article>
</section>
