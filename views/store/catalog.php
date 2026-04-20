<section class="catalog-shell">
    <aside class="catalog-filters">
        <h2>Filtrar</h2>
        <form method="get" action="/">
            <input type="hidden" name="page" value="catalog">
            <div class="field">
                <label>Buscar</label>
                <input type="search" name="q" value="<?= h($data['selected']['q']) ?>" placeholder="Nike, Outdoor, running">
            </div>
            <div class="field">
                <label>Categoria</label>
                <select name="category">
                    <option value="">Todas</option>
                    <?php foreach ($data['filters']['categories'] as $category): ?>
                        <option value="<?= h($category['category']) ?>" <?= $data['selected']['category'] === $category['category'] ? 'selected' : '' ?>>
                            <?= h($category['category']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Genero</label>
                <select name="gender">
                    <option value="">Todos</option>
                    <?php foreach ($data['filters']['genders'] as $gender): ?>
                        <option value="<?= h($gender['gender']) ?>" <?= $data['selected']['gender'] === $gender['gender'] ? 'selected' : '' ?>>
                            <?= h(gender_label($gender['gender'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Orden</label>
                <select name="sort">
                    <option value="featured" <?= $data['selected']['sort'] === 'featured' ? 'selected' : '' ?>>Destacados</option>
                    <option value="price_asc" <?= $data['selected']['sort'] === 'price_asc' ? 'selected' : '' ?>>Precio menor</option>
                    <option value="price_desc" <?= $data['selected']['sort'] === 'price_desc' ? 'selected' : '' ?>>Precio mayor</option>
                    <option value="name" <?= $data['selected']['sort'] === 'name' ? 'selected' : '' ?>>Nombre</option>
                </select>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Aplicar filtros</button>
        </form>
    </aside>

    <section>
        <div class="section-heading">
            <div>
                <h1>Catalogo</h1>
                <p><?= h((string) count($data['products'])) ?> productos visibles con stock sincronizado.</p>
            </div>
        </div>
        <div class="product-grid">
            <?php foreach ($data['products'] as $product): ?>
                <article class="store-card">
                    <a href="/?page=product&slug=<?= urlencode($product['slug']) ?>">
                        <img src="<?= h((string) $product['image_url']) ?>" alt="<?= h($product['name']) ?>">
                    </a>
                    <div class="store-card-body">
                        <div class="store-card-meta">
                            <span><?= h($product['brand']) ?></span>
                            <span><?= h($product['category']) ?></span>
                        </div>
                        <h3><a href="/?page=product&slug=<?= urlencode($product['slug']) ?>"><?= h($product['name']) ?></a></h3>
                        <p><?= h(gender_label($product['gender'])) ?> · Stock <?= h((string) $product['total_stock']) ?></p>
                        <div class="store-card-footer">
                            <strong><?= h(currency($product['price'])) ?></strong>
                            <a class="btn btn-secondary" href="/?page=product&slug=<?= urlencode($product['slug']) ?>">Ver producto</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (!$data['products']): ?>
            <div class="empty-state">No encontramos productos con esos filtros.</div>
        <?php endif; ?>
    </section>
</section>
