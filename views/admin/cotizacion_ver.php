<section class="admin-card">
    <p><a href="<?= e(url('admin/cotizaciones')); ?>">← Cotizaciones</a></p>
    <h1>#<?= (int) $cotizacion['id']; ?> · <?= e($cotizacion['nombre_contacto']); ?></h1>
    <p><?= e($cotizacion['email']); ?> · <?= e($cotizacion['telefono']); ?>
        <?php if ($cotizacion['quiere_whatsapp']): ?> · pidió WhatsApp<?php endif; ?></p>
    <p><b>Tipo:</b> <?= e($cotizacion['tipo_nombre'] ?? '—'); ?> ·
       <b>Material:</b> <?= e($cotizacion['material_nombre'] ?? '—'); ?> ·
       <b>Color:</b> <?= e($cotizacion['color'] ?? '—'); ?></p>
    <p><b>Medidas:</b> <?= e($cotizacion['largo_cm']); ?> × <?= e($cotizacion['ancho_cm']); ?> × <?= e($cotizacion['alto_cm']); ?> cm</p>
    <p><b>Presupuesto:</b> $<?= number_format((float) $cotizacion['presupuesto'], 0, ',', '.'); ?></p>
    <p><?= nl2br(e($cotizacion['descripcion'])); ?></p>
    <?php foreach ($cotizacion['imagenes'] as $img): ?>
        <p><img src="<?= e(media($img['ruta'])); ?>" alt="" style="max-width:220px;border-radius:10px"></p>
    <?php endforeach; ?>
</section>

<?php foreach ($cotizacion['respuestas'] as $r): ?>
    <section class="admin-card">
        <p><b><?= e($r['admin_nombre'] ?? 'Taller'); ?></b> · <?= e($r['created_at']); ?></p>
        <p><?= nl2br(e($r['mensaje'])); ?></p>
    </section>
<?php endforeach; ?>

<section class="admin-card">
    <h2>Responder al cliente</h2>
    <form method="POST" action="<?= e(url('admin/cotizaciones/responder')); ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
        <input type="hidden" name="id" value="<?= (int) $cotizacion['id']; ?>">
        <textarea class="field" name="mensaje" rows="5" required placeholder="Precio, tiempos, materiales..."></textarea>
        <button class="btn" type="submit">Enviar respuesta por correo</button>
        <a class="btn btn-ghost" href="https://wa.me/57<?= e(preg_replace('/\D+/', '', $cotizacion['telefono'])); ?>" target="_blank" rel="noopener">WhatsApp</a>
    </form>
</section>
