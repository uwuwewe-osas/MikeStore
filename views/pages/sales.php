<?php
$sales = $data['sales'];
$saleDetail = $data['saleDetail'];
?>
<section class="grid cols-2">
    <article class="card">
        <div class="toolbar">
            <h2>Historial de ventas</h2>
            <a class="btn btn-primary" href="/?page=pos">Nueva venta</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Cliente</th>
                        <th>Cajero</th>
                        <th>Canal</th>
                        <th>Pago</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td>
                                <a href="/?page=sales&sale_id=<?= h((string) $sale['id']) ?>">
                                    <strong><?= h($sale['sale_number']) ?></strong>
                                </a>
                            </td>
                            <td><?= h((string) ($sale['customer_name'] ?: 'Cliente eventual')) ?></td>
                            <td><?= h($sale['user_name']) ?></td>
                            <td><span class="<?= badge_class($sale['sales_channel']) ?>"><?= h($sale['sales_channel']) ?></span></td>
                            <td><span class="<?= badge_class($sale['payment_method']) ?>"><?= h($sale['payment_method']) ?></span></td>
                            <td><?= h(currency($sale['total'])) ?></td>
                            <td><?= h($sale['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="card">
        <h2>Detalle de venta</h2>
        <?php if (!$saleDetail): ?>
            <div class="empty-state">Selecciona una venta para ver su detalle.</div>
        <?php else: ?>
            <p><strong><?= h($saleDetail['sale_number']) ?></strong></p>
            <p class="muted">Cliente: <?= h((string) ($saleDetail['customer_name'] ?: 'Cliente eventual')) ?></p>
            <p class="muted">Atendido por: <?= h($saleDetail['user_name']) ?></p>
            <p class="muted">Canal: <?= h($saleDetail['sales_channel']) ?></p>
            <p class="muted">Pago: <?= h($saleDetail['payment_method']) ?></p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Cant.</th>
                            <th>Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saleDetail['items'] as $item): ?>
                            <tr>
                                <td><?= h($item['product_name']) ?></td>
                                <td><?= h($item['size_label']) ?></td>
                                <td><?= h((string) $item['quantity']) ?></td>
                                <td><?= h(currency($item['unit_price'])) ?></td>
                                <td><?= h(currency($item['line_total'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="totals">
                <div><span>Subtotal</span><strong><?= h(currency($saleDetail['subtotal'])) ?></strong></div>
                <div><span>Descuento</span><strong><?= h(currency($saleDetail['discount'])) ?></strong></div>
                <div><span>Impuesto</span><strong><?= h(currency($saleDetail['tax'])) ?></strong></div>
                <div class="grand-total"><span>Total</span><strong><?= h(currency($saleDetail['total'])) ?></strong></div>
            </div>
        <?php endif; ?>
    </article>
</section>
