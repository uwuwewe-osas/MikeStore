<?php
$sessions = $data['sessions'];
$openCash = $data['openCash'];
?>
<section class="grid cols-2">
    <article class="card">
        <?php if (!$openCash): ?>
            <h2>Abrir caja</h2>
            <form method="post" action="/?page=cash">
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="open_cash">

                <div class="field">
                    <label>Monto de apertura</label>
                    <input type="number" step="0.01" min="0" name="opening_amount" value="<?= h((string) old('opening_amount', '100.00')) ?>" required>
                </div>

                <button class="btn btn-primary" type="submit">Abrir caja</button>
            </form>
        <?php else: ?>
            <h2>Cerrar caja actual</h2>
            <p class="muted">Caja abierta el <?= h($openCash['opened_at']) ?> con fondo <?= h(currency($openCash['opening_amount'])) ?>.</p>

            <form method="post" action="/?page=cash">
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="close_cash">
                <input type="hidden" name="cash_session_id" value="<?= h((string) $openCash['id']) ?>">

                <div class="field">
                    <label>Monto contado al cierre</label>
                    <input type="number" step="0.01" min="0" name="closing_amount" value="<?= h((string) old('closing_amount', $openCash['opening_amount'])) ?>" required>
                </div>
                <div class="field">
                    <label>Nota</label>
                    <input name="note" value="<?= h((string) old('note', 'Cierre normal del turno')) ?>">
                </div>
                <button class="btn btn-danger" type="submit">Cerrar caja</button>
            </form>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Historial de cajas</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Esperado</th>
                        <th>Diferencia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td><?= h($session['user_name']) ?></td>
                            <td><?= h(currency($session['opening_amount'])) ?></td>
                            <td><?= h(currency($session['closing_amount'] ?? 0)) ?></td>
                            <td><?= h(currency($session['expected_amount'] ?? 0)) ?></td>
                            <td><?= h(currency($session['difference_amount'] ?? 0)) ?></td>
                            <td><span class="<?= badge_class($session['status']) ?>"><?= h($session['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
