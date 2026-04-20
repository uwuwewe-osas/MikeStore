<?php $variants = $data['variants']; ?>
<section class="grid cols-2">
    <article class="card">
        <h2>Ajuste de inventario</h2>
        <form method="post" action="/?page=inventory">
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action" value="adjust_inventory">

            <div class="field">
                <label>Variante</label>
                <select name="variant_id" required>
                    <?php foreach ($variants as $variant): ?>
                        <option value="<?= h((string) $variant['id']) ?>">
                            <?= h($variant['sku']) ?> · <?= h($variant['product_name']) ?> · Talla <?= h($variant['size_label']) ?> · Stock <?= h((string) $variant['stock']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Cantidad</label>
                    <input type="number" name="quantity" value="<?= h((string) old('quantity', '1')) ?>" required>
                </div>
                <div class="field">
                    <label>Nota</label>
                    <input name="note" value="<?= h((string) old('note', 'Ajuste manual')) ?>" required>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Aplicar ajuste</button>
        </form>
    </article>

    <article class="card">
        <h2>Stock por talla</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Talla</th>
                        <th>Stock</th>
                        <th>Min.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($variants as $variant): ?>
                        <tr>
                            <td><?= h($variant['sku']) ?></td>
                            <td><?= h($variant['product_name']) ?></td>
                            <td><?= h($variant['size_label']) ?></td>
                            <td>
                                <span class="<?= badge_class((int) $variant['stock'] <= (int) $variant['min_stock'] ? 'cancelled' : 'completed') ?>">
                                    <?= h((string) $variant['stock']) ?>
                                </span>
                            </td>
                            <td><?= h((string) $variant['min_stock']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
