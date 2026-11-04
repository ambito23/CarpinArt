<?php

class AdminController {
    private function guard(): void {
        Session::requireAdmin();
    }

    private function view(string $vista, array $data = []): void {
        $this->guard();
        $data['mensajesNuevos'] = (new MensajeContacto())->noLeidos();
        $data['cotizacionesNuevas'] = (int) Database::getInstance()
            ->query("SELECT COUNT(*) FROM cotizaciones WHERE estado = 'pendiente'")
            ->fetchColumn();
        $data['pedidosNuevos'] = (new Pedido())->pendientes();
        extract($data);
        require BASE_PATH . '/views/admin/layout_header.php';
        require BASE_PATH . '/views/admin/' . $vista . '.php';
        require BASE_PATH . '/views/admin/layout_footer.php';
    }

    public function dashboard(): void {
        $pedidoModel = new Pedido();
        $cotizacionModel = new Cotizacion();

        $this->view('dashboard', [
            'totalProductos'         => (int) Database::getInstance()->query('SELECT COUNT(*) FROM productos')->fetchColumn(),
            'totalCotizaciones'      => (int) Database::getInstance()->query('SELECT COUNT(*) FROM cotizaciones')->fetchColumn(),
            'totalPedidos'           => $pedidoModel->total(),
            'totalMensajes'          => (int) Database::getInstance()->query('SELECT COUNT(*) FROM mensajes_contacto')->fetchColumn(),
            'cotizacionesPorEstado'  => $cotizacionModel->contarPorEstado(),
            'cotizacionesRecientes'  => $cotizacionModel->recientes(8),
            'pedidosPorEstado'       => $pedidoModel->contarPorEstado(),
            'pedidosEnEspera'        => $pedidoModel->enEspera(),
            'pedidosCompletados'     => $pedidoModel->completados(),
            'pedidosPendientes'      => $pedidoModel->pendientes(),
            'topPedidos'             => $pedidoModel->topVendidos(5),
            'transaccionesRecientes' => $pedidoModel->recientes(12),
            'totalVentas'            => $pedidoModel->totalVentas(),
            'ventasMes'              => $pedidoModel->ventasDelMes(),
            'ticketPromedio'         => $pedidoModel->ticketPromedio(),
            'ventasMensuales'        => $pedidoModel->ventasMensuales(6),
        ]);
    }

    public function mensajes(): void {
        $this->view('mensajes', ['mensajes' => (new MensajeContacto())->todos()]);
    }

    public function mensajeLeido(): void {
        $this->guard();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            (new MensajeContacto())->marcarLeido($id);
        }
        header('Location: ' . url('admin/mensajes'));
        exit;
    }

    public function cotizaciones(): void {
        $this->view('cotizaciones', ['cotizaciones' => (new Cotizacion())->todas()]);
    }

    public function cotizacionVer(): void {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $cotizacion = $id ? (new Cotizacion())->buscar($id) : null;
        if (!$cotizacion) {
            Session::setFlash('error', 'Cotización no encontrada.');
            header('Location: ' . url('admin/cotizaciones'));
            exit;
        }
        $csrfToken = Security::generateCSRFToken();
        $this->view('cotizacion_ver', compact('cotizacion', 'csrfToken'));
    }

    public function cotizacionResponder(): void {
        $this->guard();
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token inválido.');
            header('Location: ' . url('admin/cotizaciones'));
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $mensaje = Security::sanitizeString($_POST['mensaje'] ?? '');
        $cotizacion = $id ? (new Cotizacion())->buscar($id) : null;

        if (!$cotizacion || $mensaje === '') {
            Session::setFlash('error', 'Escribe una respuesta.');
            header('Location: ' . url('admin/cotizaciones/ver?id=' . (int) $id));
            exit;
        }

        (new Cotizacion())->responder($id, (int) Session::user('user_id'), $mensaje);
        Mailer::respuestaCotizacion($cotizacion['email'], $cotizacion['nombre_contacto'], $mensaje);
        Session::setFlash('success', 'Respuesta enviada al cliente.');
        header('Location: ' . url('admin/cotizaciones/ver?id=' . $id));
        exit;
    }

    public function pedidos(): void {
        $this->view('pedidos', ['pedidos' => (new Pedido())->todos()]);
    }

    public function pedidoVer(): void {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $pedido = $id ? (new Pedido())->buscar($id) : null;
        if (!$pedido) {
            Session::setFlash('error', 'Pedido no encontrado.');
            header('Location: ' . url('admin/pedidos'));
            exit;
        }
        $csrfToken = Security::generateCSRFToken();
        $this->view('pedido_ver', compact('pedido', 'csrfToken'));
    }

    public function pedidoEstado(): void {
        $this->guard();
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token inválido.');
            header('Location: ' . url('admin/pedidos'));
            exit;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $estado = Security::sanitizeString($_POST['estado'] ?? '');
        if ($id && $estado !== '') {
            (new Pedido())->cambiarEstado($id, $estado);
            Session::setFlash('success', 'Estado del pedido actualizado.');
        }
        header('Location: ' . url('admin/pedidos/ver?id=' . (int) $id));
        exit;
    }

    public function catalogo(): void {
        $this->view('catalogo', ['productos' => (new Producto())->todosAdmin()]);
    }

    public function catalogoForm(): void {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $producto = $id ? (new Producto())->buscar($id) : null;
        $categorias = (new Producto())->categorias();
        $csrfToken = Security::generateCSRFToken();
        $this->view('catalogo_form', compact('producto', 'categorias', 'csrfToken'));
    }

    public function catalogoGuardar(): void {
        $this->guard();
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token inválido.');
            header('Location: ' . url('admin/catalogo'));
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $nombre = Security::sanitizeString($_POST['nombre'] ?? '');
        if ($nombre === '') {
            Session::setFlash('error', 'El nombre es obligatorio.');
            header('Location: ' . url('admin/catalogo'));
            exit;
        }

        $model = new Producto();
        $slug = $id ? ($model->buscar($id)['slug'] ?? Producto::slug($nombre)) : Producto::slug($nombre);

        $productoId = $model->guardar([
            'categoria_id' => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT) ?: 1,
            'nombre'       => $nombre,
            'slug'         => $slug,
            'descripcion'  => Security::sanitizeString($_POST['descripcion'] ?? ''),
            'precio'       => filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT) ?: 0,
            'stock'        => (int) ($_POST['stock'] ?? 0),
            'destacado'    => isset($_POST['destacado']) ? 1 : 0,
            'activo'       => isset($_POST['activo']) ? 1 : 0,
            'orden'        => (int) ($_POST['orden'] ?? 1),
        ], $id);

        if (!empty($_FILES['imagen']['name'])) {
            $ruta = Uploader::guardar($_FILES['imagen'], 'productos');
            if ($ruta) {
                $model->reemplazarImagen($productoId, $ruta, $nombre);
            } else {
                Session::setFlash('error', 'El producto se guardó, pero la foto no se pudo subir. Usa JPG, PNG o WebP.');
                header('Location: ' . url('admin/catalogo'));
                exit;
            }
        }

        Session::setFlash('success', 'Producto guardado.');
        header('Location: ' . url('admin/catalogo'));
        exit;
    }

    public function catalogoEliminar(): void {
        $this->guard();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            (new Producto())->eliminar($id);
            Session::setFlash('success', 'Producto eliminado.');
        }
        header('Location: ' . url('admin/catalogo'));
        exit;
    }

    public function contenido(): void {
        $contenido = new Contenido();
        $csrfToken = Security::generateCSRFToken();
        $this->view('contenido', [
            'secciones' => $contenido->secciones(),
            'slides'    => $contenido->slides(false),
            'galeria'   => $contenido->galeria(false),
            'csrfToken' => $csrfToken,
            'config'    => $contenido->config(),
        ]);
    }

    public function contenidoGuardar(): void {
        $this->guard();
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token inválido.');
            header('Location: ' . url('admin/contenido'));
            exit;
        }
        $model = new Contenido();
        $extrasCampos = [
            'hero'       => ['btn_cotizar', 'btn_catalogo'],
            'invitacion' => ['stat1_num', 'stat1_txt', 'stat2_num', 'stat2_txt', 'stat3_num', 'stat3_txt', 'btn_cuenta', 'btn_medida'],
            'catalogo'   => ['btn_comprar', 'btn_cotizar', 'btn_whatsapp'],
            'contacto'   => ['label_nombre', 'label_celular', 'label_mensaje', 'btn_enviar'],
            'footer'     => ['col_sitio', 'col_taller', 'copyright'],
            'nav'        => ['inicio', 'nosotros', 'catalogo', 'medida', 'login', 'logout'],
            'modales'    => ['medida_eyebrow', 'medida_titulo', 'medida_texto', 'medida_btn', 'compra_eyebrow', 'compra_titulo', 'compra_texto', 'compra_btn'],
        ];
        $claves = ['hero', 'invitacion', 'cintillo', 'galeria', 'catalogo', 'nosotros', 'compromiso', 'resenas', 'contacto', 'footer', 'nav', 'modales'];
        foreach ($claves as $clave) {
            $extra = [];
            foreach ($extrasCampos[$clave] ?? [] as $campo) {
                $extra[$campo] = Security::sanitizeString($_POST[$clave . '_' . $campo] ?? '');
            }
            $model->guardarSeccion(
                $clave,
                Security::sanitizeString($_POST[$clave . '_titulo'] ?? ''),
                Security::sanitizeString($_POST[$clave . '_subtitulo'] ?? ''),
                Security::sanitizeString($_POST[$clave . '_cuerpo'] ?? ''),
                $extra
            );
        }

        if (!empty($_POST['slide_id']) && is_array($_POST['slide_id'])) {
            foreach ($_POST['slide_id'] as $i => $slideId) {
                $imagen = Security::sanitizeString($_POST['slide_imagen'][$i] ?? '');
                if (!empty($_FILES['slide_file']['name'][$i])) {
                    $file = [
                        'name'     => $_FILES['slide_file']['name'][$i],
                        'type'     => $_FILES['slide_file']['type'][$i],
                        'tmp_name' => $_FILES['slide_file']['tmp_name'][$i],
                        'error'    => $_FILES['slide_file']['error'][$i],
                        'size'     => $_FILES['slide_file']['size'][$i],
                    ];
                    $subida = Uploader::guardar($file, 'hero');
                    if ($subida) {
                        $imagen = $subida;
                    }
                }
                $id = (int) $slideId ?: null;
                if (!$imagen && !$id) {
                    continue;
                }
                $model->guardarSlide([
                    ':titulo'    => Security::sanitizeString($_POST['slide_titulo'][$i] ?? ''),
                    ':subtitulo' => Security::sanitizeString($_POST['slide_subtitulo'][$i] ?? ''),
                    ':imagen'    => $imagen,
                    ':orden'     => (int) ($_POST['slide_orden'][$i] ?? $i + 1),
                    ':activo'    => isset($_POST['slide_activo'][$i]) ? 1 : 0,
                ], $id);
            }
        }

        if (!empty($_POST['galeria_id']) && is_array($_POST['galeria_id'])) {
            foreach ($_POST['galeria_id'] as $i => $gid) {
                $id = (int) $gid ?: null;
                if ($id && !empty($_POST['galeria_borrar'][$i])) {
                    $model->eliminarGaleriaItem($id);
                    continue;
                }

                $ruta = Security::sanitizeString($_POST['galeria_ruta'][$i] ?? '');
                $file = Uploader::archivo($_FILES, 'galeria_file', (int) $i);
                if ($file) {
                    $subida = Uploader::guardar($file, 'galeria');
                    if ($subida) {
                        if ($ruta && $ruta !== $subida) {
                            Uploader::borrar($ruta);
                        }
                        $ruta = $subida;
                    }
                }
                if (!$ruta) {
                    continue;
                }

                $model->guardarGaleriaItem([
                    ':ruta'   => $ruta,
                    ':alt'    => Security::sanitizeString($_POST['galeria_alt'][$i] ?? ''),
                    ':orden'  => (int) ($_POST['galeria_orden'][$i] ?? $i + 1),
                    ':activo' => isset($_POST['galeria_activo'][$i]) ? 1 : ($id ? 0 : 1),
                ], $id);
            }
        }

        Session::setFlash('success', 'Contenido actualizado.');
        header('Location: ' . url('admin/contenido'));
        exit;
    }

    public function resenas(): void {
        $this->view('resenas', [
            'resenas'   => (new Contenido())->resenas(false),
            'csrfToken' => Security::generateCSRFToken(),
        ]);
    }

    public function resenaGuardar(): void {
        $this->guard();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        (new Contenido())->guardarResena([
            ':nombre_autor' => Security::sanitizeString($_POST['nombre_autor'] ?? ''),
            ':texto'        => Security::sanitizeString($_POST['texto'] ?? ''),
            ':calificacion' => min(5, max(1, (int) ($_POST['calificacion'] ?? 5))),
            ':activo'       => isset($_POST['activo']) ? 1 : 0,
            ':orden'        => (int) ($_POST['orden'] ?? 1),
        ], $id);
        Session::setFlash('success', 'Reseña guardada.');
        header('Location: ' . url('admin/resenas'));
        exit;
    }

    public function resenaEliminar(): void {
        $this->guard();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            (new Contenido())->eliminarResena($id);
        }
        header('Location: ' . url('admin/resenas'));
        exit;
    }

    public function configuracion(): void {
        $this->view('configuracion', [
            'config'    => (new Contenido())->config(),
            'csrfToken' => Security::generateCSRFToken(),
        ]);
    }

    public function configuracionGuardar(): void {
        $this->guard();
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token inválido.');
            header('Location: ' . url('admin/configuracion'));
            exit;
        }
        $model = new Contenido();
        foreach (['whatsapp_numero', 'correo_empresa', 'instagram', 'horarios'] as $clave) {
            $model->guardarConfig($clave, Security::sanitizeString($_POST[$clave] ?? ''));
        }

        $smtp = [
            'smtp_host'      => trim($_POST['smtp_host'] ?? ''),
            'smtp_port'      => (string) ((int) ($_POST['smtp_port'] ?? 587)),
            'smtp_secure'    => in_array($_POST['smtp_secure'] ?? '', ['tls', 'ssl'], true) ? $_POST['smtp_secure'] : 'tls',
            'smtp_user'      => trim($_POST['smtp_user'] ?? ''),
            'smtp_from'      => trim($_POST['smtp_from'] ?? ''),
            'smtp_from_name' => Security::sanitizeString($_POST['smtp_from_name'] ?? ''),
        ];
        foreach ($smtp as $clave => $valor) {
            $model->guardarConfig($clave, $valor);
        }

        $pass = (string) ($_POST['smtp_pass'] ?? '');
        if (trim($pass) !== '') {
            $model->guardarConfig('smtp_pass', $pass);
        }

        Session::setFlash('success', 'Configuración guardada.');
        header('Location: ' . url('admin/configuracion'));
        exit;
    }

    public function configuracionProbar(): void {
        $this->guard();
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token inválido.');
            header('Location: ' . url('admin/configuracion'));
            exit;
        }
        $para = Security::sanitizeEmail($_POST['correo_prueba'] ?? '')
            ?: Security::sanitizeEmail((new Contenido())->config('correo_empresa') ?: MAIL_EMPRESA);

        if (!$para) {
            Session::setFlash('error', 'Indica un correo de prueba.');
            header('Location: ' . url('admin/configuracion'));
            exit;
        }

        if (!Mailer::estaConfigurado()) {
            Session::setFlash('error', 'Falta SMTP: host, usuario y contraseña de aplicación.');
            header('Location: ' . url('admin/configuracion'));
            exit;
        }

        if (Mailer::prueba($para)) {
            Session::setFlash('success', 'Correo de prueba enviado a ' . $para . '.');
        } else {
            Session::setFlash('error', 'No se pudo enviar. Revisa host, puerto, usuario y contraseña de aplicación.');
        }
        header('Location: ' . url('admin/configuracion'));
        exit;
    }
}