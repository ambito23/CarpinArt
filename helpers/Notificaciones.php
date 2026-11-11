<?php

class Notificaciones {
    /** Aviso cuando el stock está entre 1 y este valor (inclusive). */
    public const STOCK_BAJO = 2;

    /**
     * Alertas operativas del panel admin, ordenadas por prioridad.
     *
     * @return array<int, array{nivel:string,titulo:string,texto:string,url:string}>
     */
    public static function admin(): array {
        $alertas = [];
        $producto = new Producto();

        foreach ($producto->conStockBajo(self::STOCK_BAJO) as $p) {
            $stock = (int) $p['stock'];
            $nombre = (string) $p['nombre'];
            $url = url('admin/catalogo/editar?id=' . (int) $p['id']);

            if ($stock < 0) {
                $alertas[] = [
                    'nivel'  => 'danger',
                    'titulo' => 'Inventario inválido',
                    'texto'  => $nombre . ' tiene stock negativo (' . $stock . '). Corrige el inventario.',
                    'url'    => $url,
                ];
                continue;
            }

            if ($stock === 0) {
                $alertas[] = [
                    'nivel'  => 'danger',
                    'titulo' => 'Producto agotado',
                    'texto'  => $nombre . ' está en 0. Se oculta en la web hasta reponer.',
                    'url'    => $url,
                ];
                continue;
            }

            $alertas[] = [
                'nivel'  => 'warning',
                'titulo' => 'Stock bajo',
                'texto'  => $nombre . ' tiene solo ' . $stock . ' unidad(es). Revisa reposición.',
                'url'    => $url,
            ];
        }

        $pedidosPendientes = (new Pedido())->pendientes();
        if ($pedidosPendientes > 0) {
            $alertas[] = [
                'nivel'  => 'info',
                'titulo' => 'Pedidos por atender',
                'texto'  => $pedidosPendientes . ' pedido(s) en estado pendiente.',
                'url'    => url('admin/pedidos'),
            ];
        }

        $cotizPendientes = (int) Database::getInstance()
            ->query("SELECT COUNT(*) FROM cotizaciones WHERE estado = 'pendiente'")
            ->fetchColumn();
        if ($cotizPendientes > 0) {
            $alertas[] = [
                'nivel'  => 'info',
                'titulo' => 'Cotizaciones sin respuesta',
                'texto'  => $cotizPendientes . ' solicitud(es) a medida pendientes.',
                'url'    => url('admin/cotizaciones'),
            ];
        }

        $mensajes = (new MensajeContacto())->noLeidos();
        if ($mensajes > 0) {
            $alertas[] = [
                'nivel'  => 'info',
                'titulo' => 'Mensajes nuevos',
                'texto'  => $mensajes . ' mensaje(s) de contacto sin leer.',
                'url'    => url('admin/mensajes'),
            ];
        }

        return $alertas;
    }
}
