<?php $products = $data['products']; ?>
<section class="grid cols-2">
    <article class="card">
        <h2>Nuevo producto</h2>
        <form method="post" action="/?page=products">
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action" value="create_product">

            <div class="form-grid">
                <div class="field">
                    <label>SKU</label>
                    <input name="sku" value="<?= h((string) old('sku')) ?>" required>
                </div>
                <div class="field">
                    <label>Nombre</label>
                    <input name="name" value="<?= h((string) old('name')) ?>" required>
                </div>
                <div class="field">
                    <label>Marca</label>
                    <input name="brand" value="<?= h((string) old('brand')) ?>" required>
                </div>
                <div class="field">
                    <label>Categoria</label>
                    <input name="category" value="<?= h((string) old('category')) ?>" required>
                </div>
                <div class="field">
                    <label>Genero</label>
                    <select name="gender" required>
                        <option value="hombre">Hombre</option>
                        <option value="mujeres">Mujeres</option>
                        <option value="niños">Ninos</option>
                    </select>
                </div>
                <div class="field">
                    <label>Talla inicial</label>
                    <input name="size_label" value="<?= h((string) old('size_label')) ?>" required>
                </div>
                <div class="field">
                    <label>Costo</label>
                    <input name="cost" type="number" step="0.01" min="0" value="<?= h((string) old('cost', '0')) ?>" required>
                </div>
                <div class="field">
                    <label>Precio</label>
                    <input name="price" type="number" step="0.01" min="0" value="<?= h((string) old('price', '0')) ?>" required>
                </div>
                <div class="field">
                    <label>Codigo de barras</label>
                    <input name="barcode" value="<?= h((string) old('barcode')) ?>">
                </div>
                <div class="field">
                    <label>Stock inicial</label>
                    <input name="stock" type="number" min="0" value="<?= h((string) old('stock', '0')) ?>" required>
                </div>
                <div class="field">
                    <label>Stock minimo</label>
                    <input name="min_stock" type="number" min="0" value="<?= h((string) old('min_stock', '2')) ?>" required>
                </div>
                <div class="field" style="justify-content:end;">
                    <label>
                        <input type="checkbox" name="is_active" checked>
                        Activo
                    </label>
                </div>
            </div>

            <div class="field">
                <label>Descripcion</label>
                <textarea name="description"><?= h((string) old('description')) ?></textarea>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Guardar producto</button>
            </div>
        </form>
    </article>

    <article class="card">
        <h2>Catalogo actual</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= h($product['sku']) ?></td>
                            <td>
                                <strong><?= h($product['name']) ?></strong><br>
                                <span class="muted small"><?= h($product['brand']) ?> · <?= h($product['category']) ?></span>
                            </td>
                            <td><?= h(currency($product['price'])) ?></td>
                            <td><?= h((string) $product['total_stock']) ?> en <?= h((string) $product['variants_count']) ?> tallas</td>
                            <td><span class="<?= badge_class((int) $product['is_active'] === 1 ? 'completed' : 'cancelled') ?>"><?= (int) $product['is_active'] === 1 ? 'Activo' : 'Inactivo' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
