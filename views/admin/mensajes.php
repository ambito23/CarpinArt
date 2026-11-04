<section class="admin-card">
    <h1>Mensajes de contacto</h1>
    <table class="admin-table">
        <thead><tr><th>Fecha</th><th>Nombre</th><th>Celular</th><th>Mensaje</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($mensajes as $m): ?>
            <tr>
                <td><?= e($m['created_at']); ?></td>
                <td><?= e($m['nombre']); ?><?= $m['leido'] ? '' : ' · nuevo'; ?></td>
                <td><a href="https://wa.me/57<?= e(preg_replace('/\D+/', '', $m['celular'])); ?>" target="_blank" rel="noopener"><?= e($m['celular']); ?></a></td>
                <td><?= nl2br(e($m['mensaje'])); ?></td>
                <td>
                    <?php if (!$m['leido']): ?>
                    <form method="POST" action="<?= e(url('admin/mensajes/leido')); ?>">
                        <input type="hidden" name="id" value="<?= (int) $m['id']; ?>">
                        <button class="btn btn-ghost" type="submit">Marcar leído</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
