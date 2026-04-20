<?php
declare(strict_types=1);

$storeTitles = [
    'home' => ['Mike Store', 'Sneakers, lifestyle y sportwear conectados al inventario real.'],
    'catalog' => ['Catalogo', 'Explora el stock disponible en tiempo real.'],
    'product' => ['Detalle de producto', 'Elige talla y agrega al carrito.'],
    'cart' => ['Carrito', 'Revisa tu pedido antes de pagar.'],
    'checkout' => ['Checkout', 'Completa tus datos y confirma tu compra.'],
    'order-success' => ['Pedido confirmado', 'Tu orden ya fue registrada en la base del sistema.'],
];

[$storeTitle, $storeSubtitle] = $storeTitles[$page] ?? ['Mike Store', 'Tienda online conectada al POS.'];
$categoriesNav = storefront_featured_categories();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($storeTitle) ?> | Mike Store</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body class="store-body">
    <header class="store-header">
        <div class="store-topbar">
            <a class="store-brand" href="/">Mike Store</a>
            <form class="store-search" method="get" action="/">
                <input type="hidden" name="page" value="catalog">
                <input type="search" name="q" value="<?= h((string) ($_GET['q'] ?? '')) ?>" placeholder="Busca por marca, modelo o categoria">
            </form>
            <nav class="store-actions">
                <a href="/?page=catalog">Catalogo</a>
                <a href="/?page=cart">Carrito <span class="store-cart-pill"><?= h((string) $storeCart['count']) ?></span></a>
                <a href="/?page=login">POS</a>
            </nav>
        </div>
        <div class="store-nav">
            <?php foreach ($categoriesNav as $categoryItem): ?>
                <a href="/?page=catalog&category=<?= urlencode($categoryItem['category']) ?>"><?= h($categoryItem['category']) ?></a>
            <?php endforeach; ?>
        </div>
    </header>

    <main class="store-main">
        <?php if ($flashes): ?>
            <div class="flash-wrap store-flashes">
                <?php foreach ($flashes as $flash): ?>
                    <div class="flash <?= $flash['type'] === 'success' ? 'flash-success' : 'flash-error' ?>">
                        <?= h($flash['message']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php require __DIR__ . '/store/' . $page . '.php'; ?>
    </main>

    <footer class="store-footer">
        <div>
            <strong>Mike Store</strong>
            <p><?= h($storeSubtitle) ?></p>
        </div>
        <div>
            <p>Conectado a la misma base del POS para stock y ventas reales.</p>
            <a href="/?page=login">Ir al panel de administracion</a>
        </div>
    </footer>
</body>
</html>
