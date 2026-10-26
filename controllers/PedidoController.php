<?php

class PedidoController {
    public function store(): void {
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Sesión expirada. Envía el pedido otra vez.');
            header('Location: ' . url('#catalogo'));
            exit;
        }

        $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
        $producto = $productoId ? (new Producto())->buscar($productoId) : null;
        if (!$producto || !(int) $producto['activo'] || (int) $producto['stock'] < 1) {
            Session::setFlash('error', 'Esa pieza ya no está disponible.');
            header('Location: ' . url('#catalogo'));
            exit;
        }

        $cantidad = max(1, (int) ($_POST['cantidad'] ?? 1));
        $stock = (int) $producto['stock'];
        if ($cantidad > $stock) {
            Session::setFlash('error', 'Solo hay ' . $stock . ' unidad(es) de ' . $producto['nombre'] . '.');
            header('Location: ' . url('#catalogo'));
            exit;
        }

        $nombre = Security::sanitizeString($_POST['nombre_contacto'] ?? '');
        $telefono = Security::sanitizeString($_POST['telefono'] ?? '');
        $email = Security::sanitizeEmail($_POST['email'] ?? '');
        $direccion = Security::sanitizeString($_POST['direccion'] ?? '');

        if ($nombre === '' || $telefono === '' || !$email || $direccion === '') {
            Session::setFlash('error', 'Nombre, teléfono, correo y dirección son obligatorios.');
            header('Location: ' . url('#catalogo'));
            exit;
        }

        $precio = (float) $producto['precio'];
        $id = (new Pedido())->crear([
            ':producto_id'      => (int) $producto['id'],
            ':producto_nombre'  => $producto['nombre'],
            ':cantidad'         => $cantidad,
            ':precio_unitario'  => $precio,
            ':total'            => $precio * $cantidad,
            ':usuario_id'       => Session::isLoggedIn() ? Session::user('user_id') : null,
            ':nombre_contacto'  => $nombre,
            ':telefono'         => $telefono,
            ':email'            => $email,
            ':ciudad'           => Security::sanitizeString($_POST['ciudad'] ?? '') ?: null,
            ':direccion'        => $direccion,
            ':notas'            => Security::sanitizeString($_POST['notas'] ?? '') ?: null,
            ':quiere_whatsapp'  => isset($_POST['quiere_whatsapp']) ? 1 : 0,
        ]);

        (new Producto())->ajustarStock((int) $producto['id'], -$cantidad);

        Mailer::confirmacionPedido([
            'id'               => $id,
            'producto_nombre'  => $producto['nombre'],
            'cantidad'         => $cantidad,
            'total'            => $precio * $cantidad,
            'nombre_contacto'  => $nombre,
            'email'            => $email,
            'telefono'         => $telefono,
            'ciudad'           => $_POST['ciudad'] ?? '',
            'direccion'        => $direccion,
            'notas'            => $_POST['notas'] ?? '',
        ]);

        Session::setFlash('success', 'Pedido enviado. El taller te confirma disponibilidad y forma de pago.');
        header('Location: ' . url('#catalogo'));
        exit;
    }
}
