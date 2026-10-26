<?php $p = $producto ?? []; ?>
<section class="admin-card">
    <h1><?= $p ? 'Editar producto' : 'Nuevo producto'; ?></h1>
    <form method="POST" action="<?= e(url('admin/catalogo/guardar')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
        <input type="hidden" name="id" value="<?= (int) ($p['id'] ?? 0); ?>">
        <label>Nombre<input class="field" name="nombre" required value="<?= e($p['nombre'] ?? ''); ?>"></label>
        <label>Categoría
            <select class="field" name="categoria_id">
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= (int) $c['id']; ?>" <?= ((int)($p['categoria_id'] ?? 0) === (int)$c['id']) ? 'selected' : ''; ?>><?= e($c['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Descripción<textarea class="field" name="descripcion" rows="4"><?= e($p['descripcion'] ?? ''); ?></textarea></label>
        <div class="grid-2">
            <label>Precio<input class="field" type="number" step="0.01" name="precio" value="<?= e($p['precio'] ?? '0'); ?>"></label>
            <label>Stock<input class="field" type="number" name="stock" value="<?= e($p['stock'] ?? '0'); ?>"></label>
            <label>Orden<input class="field" type="number" name="orden" value="<?= e($p['orden'] ?? '1'); ?>"></label>
        </div>
        <label><input type="checkbox" name="destacado" <?= !empty($p['destacado']) ? 'checked' : ''; ?>> Mostrar en el home (máx. 6 destacados)</label>
        <label><input type="checkbox" name="activo" <?= !isset($p['activo']) || $p['activo'] ? 'checked' : ''; ?>> Activo</label>
        <?php
        $fotoActual = !empty($p['imagenes'][0]['ruta']) ? $p['imagenes'][0]['ruta'] : '';
        if ($fotoActual):
        ?>
            <p>Foto actual</p>
            <img src="<?= e(media($fotoActual)); ?>" alt="" style="width:160px;height:120px;object-fit:cover;border-radius:10px;display:block;margin:.35rem 0 1rem">
        <?php endif; ?>
        <label><?= $fotoActual ? 'Cambiar foto' : 'Foto'; ?><input class="field" type="file" name="imagen" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"></label>
        <button class="btn" type="submit">Guardar</button>
        <a class="btn btn-ghost" href="<?= e(url('admin/catalogo')); ?>">Cancelar</a>
    </form>
</section>
