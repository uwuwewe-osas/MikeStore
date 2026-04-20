<section class="card success-card">
    <span class="badge badge-success">Pedido registrado</span>
    <h1>Compra confirmada</h1>
    <p>Tu pedido online ya fue grabado en la misma base del POS, por lo que el stock y los reportes quedaron actualizados.</p>
    <div class="success-order-number"><?= h($data['orderNumber']) ?></div>
    <div class="actions">
        <a class="btn btn-primary" href="/?page=catalog">Seguir comprando</a>
        <a class="btn btn-secondary" href="/?page=login">Entrar al POS</a>
    </div>
</section>
