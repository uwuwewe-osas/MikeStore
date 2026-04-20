<section class="hero-store">
    <div class="hero-copy">
        <span class="eyebrow">Stock sincronizado con el POS</span>
        <h1>Zapatillas con energia de calle, running y aventura.</h1>
        <p>La tienda online ya usa la misma base de datos del sistema de caja, inventario y reportes. Lo que ves aqui es stock real.</p>
        <div class="actions">
            <a class="btn btn-primary" href="/?page=catalog">Ver catalogo completo</a>
            <a class="btn btn-secondary" href="/?page=cart">Ir al carrito</a>
        </div>
    </div>
    <div class="hero-panel">
        <div class="hero-stat">
            <span>Modelos activos</span>
            <strong><?= h((string) count($data['new_arrivals'])) ?>+</strong>
        </div>
        <div class="hero-stat">
            <span>Categorias</span>
            <strong><?= h((string) count($data['categories'])) ?></strong>
        </div>
        <div class="hero-stat">
            <span>Canal</span>
            <strong>Web + POS</strong>
        </div>
    </div>
</section>

<section class="store-section">
    <div class="section-heading">
        <h2>Explora por categoria</h2>
        <a href="/?page=catalog">Ver todo</a>
    </div>
    <div class="category-grid">
        <?php foreach ($data['categories'] as $category): ?>
            <a class="category-card" href="/?page=catalog&category=<?= urlencode($category['category']) ?>">
                <span><?= h($category['category']) ?></span>
                <strong><?= h((string) $category['total_products']) ?> productos</strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="store-section">
    <div class="section-heading">
        <h2>Productos destacados</h2>
        <a href="/?page=catalog&sort=featured">Seleccion curada</a>
    </div>
    <div class="product-grid">
        <?php foreach ($data['featured'] as $product): ?>
            <article class="store-card">
                <a href="/?page=product&slug=<?= urlencode($product['slug']) ?>">
                    <img src="<?= h((string) $product['image_url']) ?>" alt="<?= h($product['name']) ?>">
                </a>
                <div class="store-card-body">
                    <div class="store-card-meta">
                        <span><?= h($product['brand']) ?></span>
                        <span><?= h(gender_label($product['gender'])) ?></span>
                    </div>
                    <h3><a href="/?page=product&slug=<?= urlencode($product['slug']) ?>"><?= h($product['name']) ?></a></h3>
                    <p><?= h($product['category']) ?> · Stock <?= h((string) $product['total_stock']) ?></p>
                    <div class="store-card-footer">
                        <strong><?= h(currency($product['price'])) ?></strong>
                        <a class="btn btn-secondary" href="/?page=product&slug=<?= urlencode($product['slug']) ?>">Ver</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="store-section">
    <div class="section-heading">
        <h2>Nuevos ingresos</h2>
        <a href="/?page=catalog&sort=name">Comprar ahora</a>
    </div>
    <div class="ticker-grid">
        <?php foreach ($data['new_arrivals'] as $product): ?>
            <a class="ticker-card" href="/?page=product&slug=<?= urlencode($product['slug']) ?>">
                <img src="<?= h((string) $product['image_url']) ?>" alt="<?= h($product['name']) ?>">
                <div>
                    <strong><?= h($product['name']) ?></strong>
                    <span><?= h(currency($product['price'])) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
