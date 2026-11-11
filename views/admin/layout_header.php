<?php
$here = $_GET['url'] ?? 'admin';
$item = function (string $path, string $label) use ($here): string {
    $active = $path === 'admin'
        ? ($here === 'admin')
        : ($here === $path || str_starts_with((string) $here, $path . '/') || str_starts_with((string) $here, $path . '?'));
    $cls = $active ? 'is-active' : '';
    return '<a class="' . $cls . '" href="' . e(url($path)) . '">' . e($label) . '</a>';
};
$errorFlash = Session::getFlash('error');
$successFlash = Session::getFlash('success');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel · CarpinArt</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('public/assets/css/admin.css')); ?>?v=9">
</head>
<body class="admin-body">
<aside class="admin-side">
    <a class="admin-logo" href="<?= e(url('admin')); ?>">
        <img src="<?= e(url('public/assets/img/logo-sm.png')); ?>?v=3" alt="CarpinArt" width="196" height="129" decoding="async">
    </a>
    <p class="admin-label">Taller</p>
    <?= $item('admin', 'Dashboard'); ?>
    <?= $item('admin/contenido', 'Landing'); ?>
    <?= $item('admin/catalogo', 'Catálogo'); ?>
    <?= $item('admin/pedidos', 'Pedidos' . (!empty($pedidosNuevos) ? ' (' . $pedidosNuevos . ')' : '')); ?>
    <?= $item('admin/cotizaciones', 'Cotizaciones' . (!empty($cotizacionesNuevas) ? ' (' . $cotizacionesNuevas . ')' : '')); ?>
    <?= $item('admin/mensajes', 'Mensajes' . (!empty($mensajesNuevos) ? ' (' . $mensajesNuevos . ')' : '')); ?>
    <?= $item('admin/resenas', 'Reseñas'); ?>
    <?= $item('admin/configuracion', 'Ajustes'); ?>
    <a href="<?= e(url()); ?>" target="_blank" rel="noopener">Ver sitio</a>
    <a href="<?= e(url('logout')); ?>">Salir</a>
</aside>
<main class="admin-main">
    <div class="admin-topbar">
        <div class="admin-notif" data-notif>
            <button type="button" class="admin-notif-btn" data-notif-toggle aria-expanded="false" aria-haspopup="true">
                Alertas
                <?php if (!empty($adminAlertasCount)): ?>
                    <span class="admin-notif-count"><?= (int) $adminAlertasCount; ?></span>
                <?php endif; ?>
            </button>
            <div class="admin-notif-panel" data-notif-panel hidden>
                <p class="admin-notif-title">Notificaciones del taller</p>
                <?php if (empty($adminAlertas)): ?>
                    <p class="admin-notif-empty">Sin alertas por ahora.</p>
                <?php else: ?>
                    <ul class="admin-notif-list">
                        <?php foreach ($adminAlertas as $alerta): ?>
                            <li class="admin-notif-item is-<?= e($alerta['nivel']); ?>">
                                <a href="<?= e($alerta['url']); ?>">
                                    <strong><?= e($alerta['titulo']); ?></strong>
                                    <span><?= e($alerta['texto']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($errorFlash): ?><div class="adm-flash err"><?= e($errorFlash); ?></div><?php endif; ?>
    <?php if ($successFlash): ?><div class="adm-flash ok"><?= e($successFlash); ?></div><?php endif; ?>
    <?php
    $alertasCriticas = array_values(array_filter($adminAlertas ?? [], static function ($a) {
        return ($a['nivel'] ?? '') === 'danger' || ($a['nivel'] ?? '') === 'warning';
    }));
    if ($alertasCriticas):
    ?>
        <section class="admin-alert-strip">
            <?php foreach (array_slice($alertasCriticas, 0, 4) as $alerta): ?>
                <a class="admin-alert-chip is-<?= e($alerta['nivel']); ?>" href="<?= e($alerta['url']); ?>">
                    <b><?= e($alerta['titulo']); ?></b>
                    <span><?= e($alerta['texto']); ?></span>
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
