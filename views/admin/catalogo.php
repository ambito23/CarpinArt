<section class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
        <h1>Catálogo</h1>
        <a class="btn" href="<?= e(url('admin/catalogo/crear')); ?>">Nuevo producto</a>
    </div>
    <p>Si el stock llega a 0, el producto se oculta en la web pero no se borra.</p>
    <table class="admin-table">
        <thead><tr><th></th><th>Producto</th><th>Precio</th><th>Stock</th><th>Visible</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?php if (!empty($p['imagen'])): ?><img src="<?= e(media($p['imagen'])); ?>" alt="" width="64" height="48" style="object-fit:cover;border-radius:6px"><?php endif; ?></td>
                <td><?= e($p['nombre']); ?><br><small><?= e($p['categoria']); ?></small></td>
                <td>$<?= number_format((float) $p['precio'], 0, ',', '.'); ?></td>
                <td><?= (int) $p['stock']; ?></td>
                <td><?= ((int)$p['activo'] && (int)$p['stock'] > 0) ? 'Sí' : 'No'; ?></td>
                <td>
                    <a class="btn btn-ghost" href="<?= e(url('admin/catalogo/editar?id=' . (int) $p['id'])); ?>">Editar</a>
                    <form method="POST" action="<?= e(url('admin/catalogo/eliminar')); ?>" style="display:inline" onsubmit="return confirm('¿Eliminar este producto?');">
                        <input type="hidden" name="id" value="<?= (int) $p['id']; ?>">
                        <button class="btn btn-ghost" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
