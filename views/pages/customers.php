<?php $customers = $data['customers']; ?>
<section class="grid cols-2">
    <article class="card">
        <h2>Nuevo cliente</h2>
        <form method="post" action="/?page=customers">
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="action" value="create_customer">

            <div class="form-grid">
                <div class="field">
                    <label>Nombre completo</label>
                    <input name="full_name" value="<?= h((string) old('full_name')) ?>" required>
                </div>
                <div class="field">
                    <label>Tipo documento</label>
                    <select name="document_type">
                        <option value="DNI">DNI</option>
                        <option value="RUC">RUC</option>
                        <option value="CE">CE</option>
                        <option value="PASSPORT">Pasaporte</option>
                    </select>
                </div>
                <div class="field">
                    <label>Numero</label>
                    <input name="document_number" value="<?= h((string) old('document_number')) ?>" required>
                </div>
                <div class="field">
                    <label>Correo</label>
                    <input type="email" name="email" value="<?= h((string) old('email')) ?>">
                </div>
                <div class="field">
                    <label>Telefono</label>
                    <input name="phone" value="<?= h((string) old('phone')) ?>">
                </div>
                <div class="field">
                    <label>Puntos</label>
                    <input type="number" min="0" name="loyalty_points" value="<?= h((string) old('loyalty_points', '0')) ?>">
                </div>
            </div>

            <div class="field">
                <label>Direccion</label>
                <textarea name="address"><?= h((string) old('address')) ?></textarea>
            </div>

            <button class="btn btn-primary" type="submit">Guardar cliente</button>
        </form>
    </article>

    <article class="card">
        <h2>Base de clientes</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Contacto</th>
                        <th>Puntos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td>
                                <strong><?= h($customer['full_name']) ?></strong><br>
                                <span class="muted small"><?= h((string) $customer['address']) ?></span>
                            </td>
                            <td><?= h($customer['document_type']) ?> <?= h($customer['document_number']) ?></td>
                            <td>
                                <?= h((string) $customer['email']) ?><br>
                                <span class="muted small"><?= h((string) $customer['phone']) ?></span>
                            </td>
                            <td><?= h((string) $customer['loyalty_points']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
