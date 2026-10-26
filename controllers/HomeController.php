
<?php

class HomeController
{
    public function index(): void
    {
        $contenido = new Contenido();
        $productoModel = new Producto();

        $secciones = $contenido->secciones();
        $slides = $contenido->slides();
        $productos = $productoModel->visiblesHome(6);
        $resenas = $contenido->resenas();
        $config = $contenido->config();

        $csrfToken = Security::generateCSRFToken();

        $cotizacionModel = new Cotizacion();
        $tipos = $cotizacionModel->tipos();
        $materiales = $cotizacionModel->materiales();

        $usuario = Session::isLoggedIn()
            ? Session::user()
            : null;

        $productoPrefill = Security::sanitizeString(
            $_GET['cotizar'] ?? ''
        );

        $galeria = $contenido->galeria();

        $this->layout(
            'home/index',
            compact(
                'secciones',
                'slides',
                'productos',
                'galeria',
                'resenas',
                'config',
                'csrfToken',
                'tipos',
                'materiales',
                'usuario',
                'productoPrefill'
            )
        );
    }

    public function presentacion(): void
    {
        $archivo = BASE_PATH . '/public/Presentacion.php';

        if (!is_file($archivo)) {
            http_response_code(404);
            echo 'No se encontró el archivo de presentación.';
            return;
        }

        require $archivo;
    }

    public function contacto(): void
    {
        if (
            !Security::validateCSRFToken(
                $_POST['csrf_token'] ?? ''
            )
        ) {
            Session::setFlash(
                'error',
                'La sesión expiró. Envía el mensaje otra vez.'
            );

            header('Location: ' . url('#contacto'));
            exit;
        }

        $nombre = Security::sanitizeString(
            $_POST['nombre'] ?? ''
        );

        $celular = Security::sanitizeString(
            $_POST['celular'] ?? ''
        );

        $mensaje = Security::sanitizeString(
            $_POST['mensaje'] ?? ''
        );

        if (
            $nombre === '' ||
            $celular === '' ||
            $mensaje === ''
        ) {
            Session::setFlash(
                'error',
                'Nombre, celular y mensaje son obligatorios.'
            );

            header('Location: ' . url('#contacto'));
            exit;
        }

        (new MensajeContacto())->crear(
            $nombre,
            $celular,
            $mensaje
        );

        Mailer::confirmacionContacto([
            'nombre' => $nombre,
            'celular' => $celular,
            'mensaje' => $mensaje
        ]);

        Session::setFlash(
            'success',
            'Mensaje enviado. Te escribimos pronto.'
        );

        header('Location: ' . url('#contacto'));
        exit;
    }

    private function layout(
        string $view,
        array $data = []
    ): void {
        extract($data);

        $config = $config ?? (new Contenido())->config();

        require BASE_PATH . '/views/layouts/header.php';
        require BASE_PATH . '/views/' . $view . '.php';
        require BASE_PATH . '/views/layouts/footer.php';
    }
}
