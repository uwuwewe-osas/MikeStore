<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(app_config()['name']) ?> | Login</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-card">
            <span class="badge badge-info">POS Local</span>
            <h1>MikeZapatillas POS</h1>
            <p>Inicia sesion para administrar ventas, inventario, caja y reportes en local.</p>

            <?php if ($flashes): ?>
                <div class="flash-wrap">
                    <?php foreach ($flashes as $flash): ?>
                        <div class="flash <?= $flash['type'] === 'success' ? 'flash-success' : 'flash-error' ?>">
                            <?= h($flash['message']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/?page=login">
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="login">

                <div class="field">
                    <label for="email">Correo</label>
                    <input id="email" type="email" name="email" value="<?= h((string) old('email', 'admin@mikepos.local')) ?>" required>
                </div>

                <div class="field">
                    <label for="password">Contrasena</label>
                    <input id="password" type="password" name="password" value="admin123" required>
                </div>

                <button class="btn btn-primary btn-block" type="submit">Entrar al sistema</button>
            </form>

            <p class="small muted" style="margin-top:18px;">
                Usuario demo: <strong>admin@mikepos.local</strong> / <strong>admin123</strong>
            </p>
        </section>
    </main>
</body>
</html>
