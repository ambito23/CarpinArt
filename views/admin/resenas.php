<section class="admin-card">
    <h1>Reseñas</h1>
    <form method="POST" action="<?= e(url('admin/resenas/guardar')); ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
        <div class="grid-2">
            <label>Nombre<input class="field" name="nombre_autor" required></label>
            <label>Calificación (1-5)<input class="field" type="number" min="1" max="5" name="calificacion" value="5"></label>
        </div>
        <label>Texto<textarea class="field" name="texto" rows="3" required></textarea></label>
        <label>Orden<input class="field" type="number" name="orden" value="1"></label>
        <label><input type="checkbox" name="activo" checked> Visible</label>
        <button class="btn" type="submit">Agregar reseña</button>
    </form>
</section>

<section class="admin-card">
    <table class="admin-table">
        <thead><tr><th>Autor</th><th>Texto</th><th>★</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($resenas as $r): ?>
            <tr>
                <td><?= e($r['nombre_autor']); ?></td>
                <td><?= e($r['texto']); ?></td>
                <td><?= (int) $r['calificacion']; ?></td>
                <td>
                    <form method="POST" action="<?= e(url('admin/resenas/eliminar')); ?>">
                        <input type="hidden" name="id" value="<?= (int) $r['id']; ?>">
                        <button class="btn btn-ghost" type="submit">Quitar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
