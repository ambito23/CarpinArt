<?php

class CotizacionController {
    public function create(): void {
        $producto = Security::sanitizeString($_GET['producto'] ?? '');
        $query = $producto !== '' ? ('?cotizar=' . rawurlencode($producto)) : '?cotizar=1';
        header('Location: ' . url() . $query);
        exit;
    }

    public function store(): void {
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Sesión expirada. Envía la cotización otra vez.');
            header('Location: ' . url('?cotizar=1'));
            exit;
        }

        $nombre = Security::sanitizeString($_POST['nombre_contacto'] ?? '');
        $telefono = Security::sanitizeString($_POST['telefono'] ?? '');
        $email = Security::sanitizeEmail($_POST['email'] ?? '');
        $descripcion = Security::sanitizeString($_POST['descripcion'] ?? '');

        if ($nombre === '' || $telefono === '' || !$email || $descripcion === '') {
            Session::setFlash('error', 'Nombre, teléfono, correo y descripción son obligatorios.');
            header('Location: ' . url('?cotizar=1'));
            exit;
        }

        $model = new Cotizacion();
        $id = $model->crear([
            ':usuario_id'      => Session::isLoggedIn() ? Session::user('user_id') : null,
            ':tipo_mueble_id'  => filter_input(INPUT_POST, 'tipo_mueble_id', FILTER_VALIDATE_INT) ?: null,
            ':material_id'     => filter_input(INPUT_POST, 'material_id', FILTER_VALIDATE_INT) ?: null,
            ':largo_cm'        => filter_input(INPUT_POST, 'largo_cm', FILTER_VALIDATE_FLOAT) ?: null,
            ':ancho_cm'        => filter_input(INPUT_POST, 'ancho_cm', FILTER_VALIDATE_FLOAT) ?: null,
            ':alto_cm'         => filter_input(INPUT_POST, 'alto_cm', FILTER_VALIDATE_FLOAT) ?: null,
            ':color'           => Security::sanitizeString($_POST['color'] ?? '') ?: null,
            ':nombre_contacto' => $nombre,
            ':telefono'        => $telefono,
            ':email'           => $email,
            ':presupuesto'     => filter_input(INPUT_POST, 'presupuesto', FILTER_VALIDATE_FLOAT) ?: null,
            ':descripcion'     => $descripcion,
            ':quiere_whatsapp' => isset($_POST['quiere_whatsapp']) ? 1 : 0,
        ]);

        if (!empty($_FILES['imagenes'])) {
            foreach (Uploader::varios($_FILES['imagenes'], 'cotizaciones') as $ruta) {
                $model->agregarImagen($id, $ruta);
            }
        }

        Mailer::confirmacionCotizacion([
            'nombre_contacto' => $nombre,
            'email'           => $email,
            'telefono'        => $telefono,
            'presupuesto'     => $_POST['presupuesto'] ?? '',
            'descripcion'     => $descripcion,
        ]);

        Session::setFlash('success', 'Cotización enviada. Te confirmamos por correo.');
        header('Location: ' . url());
        exit;
    }
}
