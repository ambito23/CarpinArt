<section class="admin-card">
    <h1>Pedidos</h1>
    <p>Compras del catálogo. El taller confirma disponibilidad y forma de pago con el cliente.</p>
    <table class="admin-table">
        <thead><tr><th>Fecha</th><th>Pieza</th><th>Cliente</th><th>Total</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><?= e($p['created_at']); ?></td>
                <td><?= e($p['producto_nombre']); ?><br><small>× <?= (int) $p['cantidad']; ?></small></td>
                <td><?= e($p['nombre_contacto']); ?><br><small><?= e($p['telefono']); ?></small></td>
                <td>$<?= number_format((float) $p['total'], 0, ',', '.'); ?></td>
                <td><?= e($p['estado']); ?></td>
                <td><a class="btn" href="<?= e(url('admin/pedidos/ver?id=' . (int) $p['id'])); ?>">Ver</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
