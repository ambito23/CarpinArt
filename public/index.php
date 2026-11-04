
<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

// Cargar Composer si está instalado.
$vendor = __DIR__ . '/../vendor/autoload.php';

if (file_exists($vendor)) {
    require_once $vendor;
}

// Carga automática de controladores, modelos y ayudantes.
spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../helpers/',
        __DIR__ . '/../config/',
        __DIR__ . '/../',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión.
Session::init();

// Crear el enrutador.
$router = new Router();

/*
|--------------------------------------------------------------------------
| INICIO Y PRESENTACIÓN
|--------------------------------------------------------------------------
*/

$router->get('/', [HomeController::class, 'index']);
$router->get('presentacion', [HomeController::class, 'presentacion']);

/*
|--------------------------------------------------------------------------
| CONTACTO
|--------------------------------------------------------------------------
*/

$router->post('contacto', [HomeController::class, 'contacto']);

/*
|--------------------------------------------------------------------------
| COTIZACIONES Y COMPRAS
|--------------------------------------------------------------------------
*/

$router->get('a-medida', [CotizacionController::class, 'create']);
$router->get('personalizados', [CotizacionController::class, 'create']);

$router->post('a-medida', [CotizacionController::class, 'store']);
$router->post('comprar', [PedidoController::class, 'store']);

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

$router->post('login', [AuthController::class, 'login']);
$router->post('registro', [AuthController::class, 'register']);
$router->get('logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: PANEL Y MENSAJES
|--------------------------------------------------------------------------
*/

$router->get('admin', [AdminController::class, 'dashboard']);

$router->get('admin/mensajes', [AdminController::class, 'mensajes']);
$router->post('admin/mensajes/leido', [AdminController::class, 'mensajeLeido']);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: COTIZACIONES
|--------------------------------------------------------------------------
*/

$router->get('admin/cotizaciones', [AdminController::class, 'cotizaciones']);
$router->get('admin/cotizaciones/ver', [AdminController::class, 'cotizacionVer']);

$router->post(
    'admin/cotizaciones/responder',
    [AdminController::class, 'cotizacionResponder']
);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: PEDIDOS
|--------------------------------------------------------------------------
*/

$router->get('admin/pedidos', [AdminController::class, 'pedidos']);
$router->get('admin/pedidos/ver', [AdminController::class, 'pedidoVer']);

$router->post(
    'admin/pedidos/estado',
    [AdminController::class, 'pedidoEstado']
);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: CATÁLOGO
|--------------------------------------------------------------------------
*/

$router->get('admin/catalogo', [AdminController::class, 'catalogo']);
$router->get('admin/catalogo/crear', [AdminController::class, 'catalogoForm']);
$router->get('admin/catalogo/editar', [AdminController::class, 'catalogoForm']);

$router->post(
    'admin/catalogo/guardar',
    [AdminController::class, 'catalogoGuardar']
);

$router->post(
    'admin/catalogo/eliminar',
    [AdminController::class, 'catalogoEliminar']
);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: CONTENIDO
|--------------------------------------------------------------------------
*/

$router->get('admin/contenido', [AdminController::class, 'contenido']);

$router->post(
    'admin/contenido/guardar',
    [AdminController::class, 'contenidoGuardar']
);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: RESEÑAS
|--------------------------------------------------------------------------
*/

$router->get('admin/resenas', [AdminController::class, 'resenas']);

$router->post(
    'admin/resenas/guardar',
    [AdminController::class, 'resenaGuardar']
);

$router->post(
    'admin/resenas/eliminar',
    [AdminController::class, 'resenaEliminar']
);

/*
|--------------------------------------------------------------------------
| ADMINISTRACIÓN: CONFIGURACIÓN
|--------------------------------------------------------------------------
*/

$router->get('admin/configuracion', [AdminController::class, 'configuracion']);

$router->post(
    'admin/configuracion/guardar',
    [AdminController::class, 'configuracionGuardar']
);

$router->post(
    'admin/configuracion/probar',
    [AdminController::class, 'configuracionProbar']
);

/*
|--------------------------------------------------------------------------
| EJECUTAR LA RUTA SOLICITADA
|--------------------------------------------------------------------------
*/

// Obtener la ruta desde la URL.
$uri = $_GET['url'] ?? '/';

// Quitar barras al principio y al final.
$uri = trim($uri, '/');

// Mantener la ruta raíz como "/".
if ($uri === '') {
    $uri = '/';
}

// Ejecutar el controlador correspondiente.
$router->dispatch($uri, $_SERVER['REQUEST_METHOD']);
