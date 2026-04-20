<?php $cart = $data['cart']; ?>
<section class="checkout-shell">
    <article class="card">
        <div class="section-heading">
            <h1>Checkout</h1>
            <a href="/?page=cart">Volver al carrito</a>
        </div>

        <?php if (!$cart['items']): ?>
            <div class="empty-state">No puedes pagar sin productos en el carrito.</div>
        <?php else: ?>
            <form method="post" action="/?page=checkout">
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="checkout_online">

                <div class="form-grid">
                    <div class="field">
                        <label>Nombre completo</label>
                        <input name="full_name" value="<?= h((string) old('full_name')) ?>" required>
                    </div>
                    <div class="field">
                        <label>Correo</label>
                        <input type="email" name="email" value="<?= h((string) old('email')) ?>" required>
                    </div>
                    <div class="field">
                        <label>Telefono</label>
                        <input name="phone" value="<?= h((string) old('phone')) ?>" required>
                    </div>
                    <div class="field">
                        <label>Tipo de documento</label>
                        <select name="document_type">
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="CE">CE</option>
                            <option value="PASSPORT">Pasaporte</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Numero de documento</label>
                        <input name="document_number" value="<?= h((string) old('document_number')) ?>" required>
                    </div>
                    <div class="field">
                        <label>Metodo de pago</label>
                        <select name="payment_method">
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                            <option value="mixed">Mixto</option>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label>Direccion de entrega</label>
                    <textarea name="address" required><?= h((string) old('address')) ?></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Confirmar pedido web</button>
            </form>
        <?php endif; ?>
    </article>

    <aside class="card">
        <h2>Resumen del pedido</h2>
        <div class="cart-list compact">
            <?php foreach ($cart['items'] as $item): ?>
                <div class="cart-row mini">
                    <img src="<?= h((string) $item['image_url']) ?>" alt="<?= h($item['name']) ?>">
                    <div>
                        <strong><?= h($item['name']) ?></strong>
                        <p>Talla <?= h($item['size_label']) ?> - x<?= h((string) $item['quantity']) ?></p>
                    </div>
                    <strong><?= h(currency($item['line_total'])) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="totals">
            <div><span>Subtotal</span><strong><?= h(currency($cart['subtotal'])) ?></strong></div>
            <div><span>IGV</span><strong><?= h(currency($cart['subtotal'] * 0.18)) ?></strong></div>
            <div class="grand-total"><span>Total</span><strong><?= h(currency($cart['subtotal'] * 1.18)) ?></strong></div>
        </div>
    </aside>
</section>
