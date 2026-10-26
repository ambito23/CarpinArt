<?php
$money = static function ($n): string {
    return '$' . number_format((float) $n, 0, ',', '.');
};
$mesesEs = [1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'];
$estadoPedido = [
    'pendiente'  => 'Pendiente',
    'confirmado' => 'Confirmado',
    'en_proceso' => 'En proceso',
    'entregado'  => 'Entregado',
    'cancelado'  => 'Cancelado',
];
$estadoCotiz = [
    'pendiente'    => 'Pendiente',
    'en_revision'  => 'En revisión',
    'respondida'   => 'Respondida',
    'cerrada'      => 'Cerrada',
];
$maxIngresos = 0;
foreach ($ventasMensuales as $vm) {
    if ((float) $vm['ingresos'] > $maxIngresos) {
        $maxIngresos = (float) $vm['ingresos'];
    }
}
$maxUnidades = 1;
foreach ($topPedidos as $tp) {
    $u = (int) $tp['unidades'];
    if ($u > $maxUnidades) {
        $maxUnidades = $u;
    }
}
$totalPedEst = array_sum($pedidosPorEstado);
$donutStops = [];
$donutColors = [
    'pendiente'  => '#c47a3a',
    'confirmado' => '#3d6b8a',
    'en_proceso' => '#8a6b3d',
    'entregado'  => '#3d6b48',
    'cancelado'  => '#8a3d3d',
];
$acc = 0;
if ($totalPedEst > 0) {
    foreach ($pedidosPorEstado as $est => $cant) {
        if ($cant <= 0) {
            continue;
        }
        $pct = ($cant / $totalPedEst) * 100;
        $from = $acc;
        $acc += $pct;
        $donutStops[] = ($donutColors[$est] ?? '#888') . ' ' . $from . '% ' . $acc . '%';
    }
}
$donutCss = $donutStops ? implode(', ', $donutStops) : '#efe6d8 0% 100%';
?>
<section class="dash">
    <header class="dash-head">
        <div>
            <p class="dash-kicker">Taller · resumen operativo</p>
            <h1>Dashboard</h1>
            <p class="dash-sub">Cotizaciones, pedidos en curso y movimiento de ventas del catálogo.</p>
        </div>
        <p class="dash-date"><?= e(date('d/m/Y')); ?></p>
    </header>

    <div class="dash-kpis">
        <a class="dash-kpi" href="<?= e(url('admin/cotizaciones')); ?>">
            <span class="dash-kpi-label">Cotizaciones</span>
            <strong><?= (int) $totalCotizaciones; ?></strong>
            <span class="dash-kpi-hint"><?= (int) ($cotizacionesPorEstado['pendiente'] ?? 0); ?> pendientes de respuesta</span>
        </a>
        <a class="dash-kpi wait" href="<?= e(url('admin/pedidos')); ?>">
            <span class="dash-kpi-label">Pedidos en espera</span>
            <strong><?= (int) $pedidosEnEspera; ?></strong>
            <span class="dash-kpi-hint"><?= (int) $pedidosPendientes; ?> nuevos · confirmados o en proceso</span>
        </a>
        <a class="dash-kpi done" href="<?= e(url('admin/pedidos')); ?>">
            <span class="dash-kpi-label">Pedidos completados</span>
            <strong><?= (int) $pedidosCompletados; ?></strong>
            <span class="dash-kpi-hint">Entregados al cliente</span>
        </a>
        <div class="dash-kpi money">
            <span class="dash-kpi-label">Ventas (sin cancelados)</span>
            <strong><?= e($money($totalVentas)); ?></strong>
            <span class="dash-kpi-hint">Este mes <?= e($money($ventasMes)); ?> · ticket <?= e($money($ticketPromedio)); ?></span>
        </div>
    </div>

    <div class="dash-grid">
        <article class="admin-card dash-card">
            <div class="dash-card-head">
                <h2>Ventas últimos 6 meses</h2>
                <span><?= (int) $totalPedidos; ?> pedidos en total</span>
            </div>
            <?php if ($maxIngresos <= 0): ?>
                <p class="dash-empty">Aún no hay ventas registradas. Cuando lleguen pedidos del catálogo, aquí verás el recaudo mes a mes.</p>
            <?php else: ?>
                <div class="dash-bars" role="img" aria-label="Ingresos por mes">
                    <?php foreach ($ventasMensuales as $vm):
                        $alto = max(4, (int) round(((float) $vm['ingresos'] / $maxIngresos) * 100));
                        $m = (int) substr($vm['mes'], 5, 2);
                        ?>
                        <div class="dash-bar-col">
                            <span class="dash-bar-val"><?= (float) $vm['ingresos'] > 0 ? e($money($vm['ingresos'])) : '—'; ?></span>
                            <div class="dash-bar" style="height: <?= $alto; ?>%"></div>
                            <span class="dash-bar-lab"><?= e($mesesEs[$m] ?? $vm['mes']); ?></span>
                            <span class="dash-bar-n"><?= (int) $vm['pedidos']; ?> ped.</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <article class="admin-card dash-card">
            <div class="dash-card-head">
                <h2>Estado de pedidos</h2>
                <span><?= (int) $totalPedEst; ?> registros</span>
            </div>
            <?php if ($totalPedEst === 0): ?>
                <p class="dash-empty">Sin pedidos todavía.</p>
            <?php else: ?>
                <div class="dash-donut-wrap">
                    <div class="dash-donut" style="background: conic-gradient(<?= e($donutCss); ?>);"></div>
                    <ul class="dash-legend">
                        <?php foreach ($pedidosPorEstado as $est => $cant): ?>
                            <li>
                                <i style="background:<?= e($donutColors[$est] ?? '#888'); ?>"></i>
                                <?= e($estadoPedido[$est] ?? $est); ?>
                                <b><?= (int) $cant; ?></b>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </article>
    </div>

    <div class="dash-grid dash-grid-bottom">
        <article class="admin-card dash-card">
            <div class="dash-card-head">
                <h2>Top productos más vendidos</h2>
                <a href="<?= e(url('admin/catalogo')); ?>">Catálogo</a>
            </div>
            <?php if (empty($topPedidos)): ?>
                <p class="dash-empty">No hay unidades vendidas para armar un ranking.</p>
            <?php else: ?>
                <ol class="dash-top">
                    <?php foreach ($topPedidos as $i => $tp):
                        $pct = (int) round(((int) $tp['unidades'] / $maxUnidades) * 100);
                        ?>
                        <li>
                            <div class="dash-top-row">
                                <span class="dash-rank"><?= $i + 1; ?></span>
                                <div>
                                    <strong><?= e($tp['producto_nombre']); ?></strong>
                                    <span><?= (int) $tp['unidades']; ?> uds · <?= (int) $tp['num_pedidos']; ?> pedidos · <?= e($money($tp['ingresos'])); ?></span>
                                </div>
                            </div>
                            <div class="dash-hbar"><i style="width:<?= $pct; ?>%"></i></div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </article>

        <article class="admin-card dash-card">
            <div class="dash-card-head">
                <h2>Reporte de cotizaciones</h2>
                <a href="<?= e(url('admin/cotizaciones')); ?>">Ver todas</a>
            </div>
            <div class="dash-mini-stats">
                <?php foreach ($estadoCotiz as $clave => $etiqueta): ?>
                    <div>
                        <b><?= (int) ($cotizacionesPorEstado[$clave] ?? 0); ?></b>
                        <?= e($etiqueta); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (empty($cotizacionesRecientes)): ?>
                <p class="dash-empty">Todavía no hay solicitudes a medida.</p>
            <?php else: ?>
                <table class="admin-table dash-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Presupuesto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cotizacionesRecientes as $c): ?>
                        <tr>
                            <td><a href="<?= e(url('admin/cotizaciones/ver?id=' . (int) $c['id'])); ?>">#<?= (int) $c['id']; ?></a></td>
                            <td>
                                <?= e($c['nombre_contacto']); ?>
                                <small><?= e(date('d/m/Y', strtotime($c['created_at']))); ?></small>
                            </td>
                            <td><?= e($c['tipo_nombre'] ?: '—'); ?></td>
                            <td><?= $c['presupuesto'] !== null && $c['presupuesto'] !== '' ? e($money($c['presupuesto'])) : '—'; ?></td>
                            <td><span class="dash-badge <?= e($c['estado']); ?>"><?= e($estadoCotiz[$c['estado']] ?? $c['estado']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </article>
    </div>

    <article class="admin-card dash-card">
        <div class="dash-card-head">
            <h2>Detalle de transacciones</h2>
            <a href="<?= e(url('admin/pedidos')); ?>">Todos los pedidos</a>
        </div>
        <?php if (empty($transaccionesRecientes)): ?>
            <p class="dash-empty">No hay transacciones. Los pedidos del catálogo aparecerán aquí con cliente, pieza, total y estado.</p>
        <?php else: ?>
            <div class="dash-table-wrap">
                <table class="admin-table dash-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Unitario</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($transaccionesRecientes as $tx): ?>
                        <tr>
                            <td>#<?= (int) $tx['id']; ?></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($tx['created_at']))); ?></td>
                            <td>
                                <?= e($tx['nombre_contacto']); ?>
                                <small><?= e($tx['telefono']); ?></small>
                            </td>
                            <td><?= e($tx['producto_nombre']); ?></td>
                            <td><?= (int) $tx['cantidad']; ?></td>
                            <td><?= e($money($tx['precio_unitario'])); ?></td>
                            <td><b><?= e($money($tx['total'])); ?></b></td>
                            <td><span class="dash-badge <?= e($tx['estado']); ?>"><?= e($estadoPedido[$tx['estado']] ?? $tx['estado']); ?></span></td>
                            <td><a class="btn" href="<?= e(url('admin/pedidos/ver?id=' . (int) $tx['id'])); ?>">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>