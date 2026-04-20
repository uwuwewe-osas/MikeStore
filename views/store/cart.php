<?php $cart = $data['cart']; ?>
<section class="checkout-shell">
    <article class="card">
        <div class="section-heading">
            <h1>Tu carrito</h1>
            <a href="/?page=catalog">Seguir comprando</a>
        </div>

        <?php if (!$cart['items']): ?>
            <div class="empty-state">Tu carrito esta vacio. Explora el catalogo y agrega una talla.</div>
        <?php else: ?>
            <div class="cart-list">
                <?php foreach ($cart['items'] as $item): ?>
                    <div class="cart-row">
                        <img src="<?= h((string) $item['image_url']) ?>" alt="<?= h($item['name']) ?>">
                        <div>
                            <strong><?= h($item['name']) ?></strong>
                            <p><?= h($item['brand']) ?> - Talla <?= h($item['size_label']) ?></p>
                            <p class="muted small">Stock disponible: <?= h((string) $item['stock']) ?></p>
                        </div>
                        <form method="post" action="/?page=cart" class="cart-update-form">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="action" value="update_cart">
                            <div class="field">
                                <label>Cantidad</label>
                                <input type="number" name="cart[<?= h((string) $item['variant_id']) ?>]" min="0" max="<?= h((string) $item['stock']) ?>" value="<?= h((string) $item['quantity']) ?>">
                            </div>
                            <button class="btn btn-secondary" type="submit">Actualizar</button>
                        </form>
                        <div>
                            <strong><?= h(currency($item['line_total'])) ?></strong>
                            <form method="post" action="/?page=cart">
                                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="action" value="remove_from_cart">
                                <input type="hidden" name="variant_id" value="<?= h((string) $item['variant_id']) ?>">
                                <button class="btn btn-danger" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="actions">
                <a class="btn btn-secondary" href="/?page=catalog">Seguir comprando</a>
                <a class="btn btn-primary" href="/?page=checkout">Ir al checkout</a>
            </div>
        <?php endif; ?>
    </article>

    <aside class="card">
        <h2>Resumen</h2>
        <div class="totals">
            <div><span>Items</span><strong><?= h((string) $cart['count']) ?></strong></div>
            <div><span>Subtotal</span><strong><?= h(currency($cart['subtotal'])) ?></strong></div>
            <div><span>IGV estimado</span><strong><?= h(currency($cart['subtotal'] * 0.18)) ?></strong></div>
            <div class="grand-total"><span>Total estimado</span><strong><?= h(currency($cart['subtotal'] * 1.18)) ?></strong></div>
        </div>
    </aside>
</section>
