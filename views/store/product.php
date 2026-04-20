<?php $product = $data['product']; ?>
<?php if (!$product): ?>
    <div class="empty-state">El producto que buscas no existe o ya no esta activo.</div>
<?php else: ?>
    <section class="product-detail">
        <div class="product-visual" style="background: linear-gradient(135deg, <?= h((string) ($product['accent_color'] ?? '#0f766e')) ?> 0%, #ffffff 100%);">
            <img src="<?= h((string) $product['image_url']) ?>" alt="<?= h($product['name']) ?>">
        </div>
        <div class="product-info-panel">
            <div class="store-card-meta">
                <span><?= h($product['brand']) ?></span>
                <span><?= h(gender_label($product['gender'])) ?></span>
            </div>
            <h1><?= h($product['name']) ?></h1>
            <p class="product-price"><?= h(currency($product['price'])) ?></p>
            <p class="product-copy"><?= h((string) $product['description']) ?></p>
            <div class="product-tags">
                <span class="badge badge-info"><?= h($product['category']) ?></span>
                <span class="<?= badge_class((int) $product['total_stock'] > 5 ? 'completed' : 'cancelled') ?>">
                    Stock total <?= h((string) $product['total_stock']) ?>
                </span>
            </div>

            <form method="post" action="/?page=product&slug=<?= urlencode($product['slug']) ?>">
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="return_to" value="/?page=product&slug=<?= h($product['slug']) ?>">

                <div class="field">
                    <label>Talla disponible</label>
                    <select name="variant_id" required>
                        <?php foreach ($product['variants'] as $variant): ?>
                            <option value="<?= h((string) $variant['id']) ?>" <?= (int) $variant['stock'] <= 0 ? 'disabled' : '' ?>>
                                Talla <?= h($variant['size_label']) ?> · Stock <?= h((string) $variant['stock']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Cantidad</label>
                    <input type="number" min="1" max="5" name="quantity" value="1" required>
                </div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Agregar al carrito</button>
                    <a class="btn btn-secondary" href="/?page=cart">Ver carrito</a>
                </div>
            </form>
        </div>
    </section>

    <section class="store-section">
        <div class="section-heading">
            <h2>Tambien te puede interesar</h2>
            <a href="/?page=catalog">Seguir comprando</a>
        </div>
        <div class="product-grid">
            <?php foreach ($product['related'] as $related): ?>
                <article class="store-card">
                    <a href="/?page=product&slug=<?= urlencode($related['slug']) ?>">
                        <img src="<?= h((string) $related['image_url']) ?>" alt="<?= h($related['name']) ?>">
                    </a>
                    <div class="store-card-body">
                        <div class="store-card-meta">
                            <span><?= h($related['brand']) ?></span>
                            <span><?= h($related['category']) ?></span>
                        </div>
                        <h3><a href="/?page=product&slug=<?= urlencode($related['slug']) ?>"><?= h($related['name']) ?></a></h3>
                        <div class="store-card-footer">
                            <strong><?= h(currency($related['price'])) ?></strong>
                            <a class="btn btn-secondary" href="/?page=product&slug=<?= urlencode($related['slug']) ?>">Ver</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
