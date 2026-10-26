<?php
$estados = [
    'pendiente'  => 'Pendiente',
    'confirmado' => 'Confirmado',
    'en_proceso' => 'En proceso',
    'entregado'  => 'Entregado',
    'cancelado'  => 'Cancelado',
];
$tel = preg_replace('/\D+/', '', $pedido['telefono'] ?? '');
?>
<section class="admin-card">
    <p><a href="<?= e(url('admin/pedidos')); ?>">← Pedidos</a></p>
    <h1>Pedido #<?= (int) $pedido['id']; ?></h1>
    <p><b><?= e($pedido['producto_nombre']); ?></b> × <?= (int) $pedido['cantidad']; ?></p>
    <p>Unitario $<?= number_format((float) $pedido['precio_unitario'], 0, ',', '.'); ?>
        · Total $<?= number_format((float) $pedido['total'], 0, ',', '.'); ?></p>
    <p><?= e($pedido['nombre_contacto']); ?> · <?= e($pedido['email']); ?> · <?= e($pedido['telefono']); ?>
        <?php if (!empty($pedido['quiere_whatsapp'])): ?> · pidió WhatsApp<?php endif; ?></p>
    <p><b>Entrega:</b> <?= e($pedido['ciudad'] ?: '—'); ?> — <?= e($pedido['direccion']); ?></p>
    <?php if (!empty($pedido['notas'])): ?>
        <p><?= nl2br(e($pedido['notas'])); ?></p>
    <?php endif; ?>
    <p><small><?= e($pedido['created_at']); ?></small></p>
</section>

<section class="admin-card">
    <h2>Estado</h2>
    <form method="POST" action="<?= e(url('admin/pedidos/estado')); ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
        <input type="hidden" name="id" value="<?= (int) $pedido['id']; ?>">
        <label>
            <select class="field" name="estado">
                <?php foreach ($estados as $valor => $etiqueta): ?>
                    <option value="<?= e($valor); ?>" <?= $pedido['estado'] === $valor ? 'selected' : ''; ?>><?= e($etiqueta); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="btn" type="submit">Actualizar estado</button>
        <?php if ($tel): ?>
            <a class="btn btn-ghost" href="https://wa.me/<?= e($tel); ?>" target="_blank" rel="noopener">WhatsApp</a>
        <?php endif; ?>
    </form>
</section>
