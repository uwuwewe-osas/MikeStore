<?php
$variants = $data['variants'];
$customers = $data['customers'];
$openCash = $data['openCash'];
?>
<section class="pos-layout">
    <article class="card">
        <div class="toolbar">
            <h2>Selector de productos</h2>
            <?php if ($openCash): ?>
                <span class="badge badge-success">Caja abierta</span>
            <?php else: ?>
                <span class="badge badge-danger">Sin caja abierta en efectivo</span>
            <?php endif; ?>
        </div>
        <div class="product-picker" id="product-picker">
            <?php foreach ($variants as $variant): ?>
                <div class="product-option">
                    <h4><?= h($variant['product_name']) ?></h4>
                    <small><?= h($variant['brand']) ?> · SKU <?= h($variant['sku']) ?></small><br>
                    <small>Talla <?= h($variant['size_label']) ?> · Stock <?= h((string) $variant['stock']) ?></small>
                    <div style="margin:10px 0 12px;font-weight:700;"><?= h(currency($variant['price'])) ?></div>
                    <div class="actions">
                        <button
                            class="btn btn-secondary"
                            type="button"
                            data-variant-id="<?= h((string) $variant['id']) ?>"
                            data-product-name="<?= h($variant['product_name']) ?>"
                            data-size-label="<?= h($variant['size_label']) ?>"
                            data-price="<?= h((string) $variant['price']) ?>"
                            data-stock="<?= h((string) $variant['stock']) ?>"
                            onclick="addItem(this)"
                        >
                            Agregar
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="card">
        <h2>Ticket actual</h2>
        <form method="post" action="/?page=pos" id="sale-form">
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action" value="create_sale">
            <input type="hidden" name="items_json" id="items_json" value="[]">

            <div class="field">
                <label>Cliente</label>
                <select name="customer_id">
                    <option value="">Cliente eventual</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= h((string) $customer['id']) ?>"><?= h($customer['full_name']) ?> · <?= h($customer['document_number']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Metodo de pago</label>
                    <select name="payment_method" id="payment_method">
                        <option value="cash">Efectivo</option>
                        <option value="card">Tarjeta</option>
                        <option value="transfer">Transferencia</option>
                        <option value="mixed">Mixto</option>
                    </select>
                </div>
                <div class="field">
                    <label>Descuento</label>
                    <input type="number" step="0.01" min="0" name="discount" id="discount" value="0">
                </div>
                <div class="field">
                    <label>Impuesto %</label>
                    <input type="number" step="0.01" min="0" name="tax_rate" id="tax_rate" value="18">
                </div>
                <div class="field">
                    <label>Notas</label>
                    <input name="notes" placeholder="Observaciones del ticket">
                </div>
            </div>

            <div class="sale-items" id="sale-items">
                <div class="empty-state">Aun no agregas productos al ticket.</div>
            </div>

            <div class="totals">
                <div><span>Subtotal</span><strong id="subtotal">S/ 0.00</strong></div>
                <div><span>Descuento</span><strong id="discount-value">S/ 0.00</strong></div>
                <div><span>Impuesto</span><strong id="tax-value">S/ 0.00</strong></div>
                <div class="grand-total"><span>Total</span><strong id="grand-total">S/ 0.00</strong></div>
            </div>

            <div class="actions" style="margin-top:16px;">
                <button class="btn btn-primary" type="submit">Registrar venta</button>
                <button class="btn btn-secondary" type="button" onclick="clearSale()">Limpiar ticket</button>
            </div>
        </form>
    </article>
</section>

<script>
    const saleItems = [];

    function toCurrency(value) {
        return 'S/ ' + Number(value).toFixed(2);
    }

    function addItem(button) {
        const variantId = Number(button.dataset.variantId);
        const stock = Number(button.dataset.stock);
        const existing = saleItems.find(item => item.variant_id === variantId);

        if (existing) {
            if (existing.quantity >= stock) {
                alert('No hay mas stock disponible para esta talla.');
                return;
            }
            existing.quantity += 1;
        } else {
            saleItems.push({
                variant_id: variantId,
                product_name: button.dataset.productName,
                size_label: button.dataset.sizeLabel,
                price: Number(button.dataset.price),
                quantity: 1,
                stock: stock
            });
        }

        renderSaleItems();
    }

    function updateQuantity(variantId, delta) {
        const item = saleItems.find(entry => entry.variant_id === variantId);
        if (!item) return;

        item.quantity += delta;
        if (item.quantity <= 0) {
            const index = saleItems.findIndex(entry => entry.variant_id === variantId);
            saleItems.splice(index, 1);
        } else if (item.quantity > item.stock) {
            item.quantity = item.stock;
        }

        renderSaleItems();
    }

    function clearSale() {
        saleItems.length = 0;
        renderSaleItems();
    }

    function renderSaleItems() {
        const container = document.getElementById('sale-items');
        if (saleItems.length === 0) {
            container.innerHTML = '<div class="empty-state">Aun no agregas productos al ticket.</div>';
        } else {
            container.innerHTML = saleItems.map(item => `
                <div class="sale-item">
                    <div>
                        <strong>${item.product_name}</strong><br>
                        <span class="muted small">Talla ${item.size_label} · Stock ${item.stock}</span>
                    </div>
                    <div style="text-align:right;">
                        <div>${toCurrency(item.price * item.quantity)}</div>
                        <div class="actions" style="justify-content:end;margin-top:8px;">
                            <button class="btn btn-secondary" type="button" onclick="updateQuantity(${item.variant_id}, -1)">-</button>
                            <span style="padding:12px 4px;">${item.quantity}</span>
                            <button class="btn btn-secondary" type="button" onclick="updateQuantity(${item.variant_id}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        recalculateTotals();
    }

    function recalculateTotals() {
        const subtotal = saleItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = Number(document.getElementById('discount').value || 0);
        const taxRate = Number(document.getElementById('tax_rate').value || 0);
        const taxable = Math.max(0, subtotal - discount);
        const tax = taxable * (taxRate / 100);
        const total = taxable + tax;

        document.getElementById('subtotal').textContent = toCurrency(subtotal);
        document.getElementById('discount-value').textContent = toCurrency(discount);
        document.getElementById('tax-value').textContent = toCurrency(tax);
        document.getElementById('grand-total').textContent = toCurrency(total);
        document.getElementById('items_json').value = JSON.stringify(
            saleItems.map(item => ({ variant_id: item.variant_id, quantity: item.quantity }))
        );
    }

    document.getElementById('discount').addEventListener('input', recalculateTotals);
    document.getElementById('tax_rate').addEventListener('input', recalculateTotals);

    document.getElementById('sale-form').addEventListener('submit', function (event) {
        const paymentMethod = document.getElementById('payment_method').value;
        if (saleItems.length === 0) {
            event.preventDefault();
            alert('Agrega al menos un producto.');
            return;
        }

        if (paymentMethod === 'cash' && <?= $openCash ? 'false' : 'true' ?>) {
            event.preventDefault();
            alert('Abre caja antes de cobrar en efectivo.');
        }
    });
</script>
