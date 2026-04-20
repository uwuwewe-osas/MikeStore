<?php
$report = $data['report'];
$summary = $report['summary'];
?>
<section class="grid cols-4">
    <article class="card stat-card">
        <div class="stat-label">Ventas registradas</div>
        <div class="stat-value"><?= h((string) ($summary['total_sales'] ?? 0)) ?></div>
    </article>
    <article class="card stat-card">
        <div class="stat-label">Ingreso total</div>
        <div class="stat-value"><?= h(currency($summary['total_revenue'] ?? 0)) ?></div>
    </article>
    <article class="card stat-card">
        <div class="stat-label">Descuentos</div>
        <div class="stat-value"><?= h(currency($summary['total_discount'] ?? 0)) ?></div>
    </article>
    <article class="card stat-card">
        <div class="stat-label">Saldo de caja</div>
        <div class="stat-value"><?= h(currency($report['cash_balance'])) ?></div>
    </article>
</section>

<section class="grid cols-2" style="margin-top:20px;">
    <article class="card">
        <h2>Ventas por medio de pago</h2>
        <?php if (!$report['payment_methods']): ?>
            <div class="empty-state">No hay ventas para mostrar.</div>
        <?php else: ?>
            <ul class="kpi-list">
                <?php foreach ($report['payment_methods'] as $method): ?>
                    <li>
                        <span><?= h($method['payment_method']) ?></span>
                        <strong><?= h((string) $method['total_sales']) ?> ventas · <?= h(currency($method['amount'])) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Ventas por canal</h2>
        <?php if (!$report['channels']): ?>
            <div class="empty-state">Aun no hay canales con ventas.</div>
        <?php else: ?>
            <ul class="kpi-list">
                <?php foreach ($report['channels'] as $channel): ?>
                    <li>
                        <span><?= h($channel['sales_channel']) ?></span>
                        <strong><?= h((string) $channel['total_sales']) ?> ventas · <?= h(currency($channel['amount'])) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Indicadores incluidos</h2>
        <ul class="kpi-list">
            <li><span>Ticket promedio</span><strong><?= h(currency(($summary['total_sales'] ?? 0) > 0 ? ((float) $summary['total_revenue'] / (int) $summary['total_sales']) : 0)) ?></strong></li>
            <li><span>Impuesto acumulado</span><strong><?= h(currency($summary['total_tax'] ?? 0)) ?></strong></li>
            <li><span>Cobertura operativa</span><strong>Ventas, caja e inventario</strong></li>
        </ul>
    </article>
</section>
