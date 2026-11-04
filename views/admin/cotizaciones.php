<section class="admin-card">
    <h1>Cotizaciones</h1>
    <table class="admin-table">
        <thead><tr><th>Fecha</th><th>Cliente</th><th>Tipo</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($cotizaciones as $c): ?>
            <tr>
                <td><?= e($c['created_at']); ?></td>
                <td><?= e($c['nombre_contacto']); ?><br><small><?= e($c['email']); ?> · <?= e($c['telefono']); ?></small></td>
                <td><?= e($c['tipo_nombre'] ?? '—'); ?></td>
                <td><?= e($c['estado']); ?></td>
                <td><a class="btn" href="<?= e(url('admin/cotizaciones/ver?id=' . (int) $c['id'])); ?>">Ver / responder</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
