
<?php

$config = $config ?? (new Contenido())->config();

$whatsapp = preg_replace(
    '/\D+/',
    '',
    $config['whatsapp_numero'] ?? '573000000000'
);

$csrfToken = $csrfToken ?? Security::generateCSRFToken();
$logged = Session::isLoggedIn();

$errorFlash = Session::getFlash('error');
$successFlash = Session::getFlash('success');

$currentUrl = url(ltrim($_GET['url'] ?? '', '/') ?: '');
$isHome = empty($_GET['url']) || $_GET['url'] === '/';

$secciones = $secciones ?? [];
$slides = $slides ?? [];

$sec = $sec ?? function (
    string $k,
    string $f,
    string $d = ''
) use (&$secciones): string {
    $valor = $secciones[$k][$f] ?? '';
    return e($valor !== '' ? $valor : $d);
};

$x = $x ?? function (
    string $k,
    string $f,
    string $d = ''
) use (&$secciones): string {
    $valor = $secciones[$k]['_extra'][$f] ?? '';
    return e($valor !== '' ? $valor : $d);
};

$tipos = $tipos ?? [];
$materiales = $materiales ?? [];

$usuario = $usuario ?? (
    Session::isLoggedIn() ? Session::user() : null
);

$productoPrefill = $productoPrefill ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CarpinArt — Muebles a medida</title>

    <meta
        name="description"
        content="CarpinArt: muebles personalizados, carpintería a medida y piezas hechas para tu espacio."
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Outfit:wght@400;500&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= e(url('public/assets/css/app.css')); ?>?v=12"
    >

    <?php if (!empty($slides[0]['imagen'])): ?>
        <link
            rel="preload"
            as="image"
            href="<?= e(media($slides[0]['imagen'], 'hero')); ?>"
        >
    <?php endif; ?>
</head>

<body>

<header
    class="site-nav<?= $isHome ? '' : ' is-scrolled'; ?>"
    id="siteNav"
>
    <div class="nav-inner">

        <a class="logo" href="<?= e(url()); ?>">
            <img
                src="<?= e(url('public/assets/img/logo-sm.png')); ?>?v=3"
                alt="CarpinArt"
                width="216"
                height="142"
                decoding="async"
            >
        </a>

        <button
            class="nav-toggle"
            id="navToggle"
            type="button"
            aria-label="Abrir menú"
            aria-controls="navLinks"
            aria-expanded="false"
        >
            ☰
        </button>

        <nav class="nav-links" id="navLinks">

            <a href="<?= e(url('#inicio')); ?>">
                <?= $x('nav', 'inicio', 'Inicio'); ?>
            </a>

            <a href="<?= e(url('#nosotros')); ?>">
                <?= $x('nav', 'nosotros', 'Nosotros'); ?>
            </a>

            <a href="<?= e(url('#catalogo')); ?>">
                <?= $x('nav', 'catalogo', 'Catálogo'); ?>
            </a>

            <button
                class="nav-link-btn"
                type="button"
                data-open-measure
            >
                <?= $x('nav', 'medida', 'A medida'); ?>
            </button>

            <?php if ($logged): ?>

                <a class="nav-cta" href="<?= e(url('logout')); ?>">
                    <?= $x('nav', 'logout', 'Cerrar sesión'); ?>
                </a>

            <?php else: ?>

                <button
                    class="nav-cta"
                    type="button"
                    data-open-auth="login"
                >
                    <?= $x('nav', 'login', 'Crear / Iniciar sesión'); ?>
                </button>

            <?php endif; ?>

            <button
                class="nav-link-btn"
                type="button"
                id="btnTratamientoDatos"
                aria-controls="modalTratamientoDatos"
            >
                Tratamiento de datos
            </button>

        </nav>
    </div>
</header>

<!-- Modal de tratamiento de datos -->
<div
    class="sheet-modal"
    id="modalTratamientoDatos"
    role="dialog"
    aria-modal="true"
    aria-labelledby="tituloTratamientoDatos"
    aria-hidden="true"
    hidden
>
    <div class="sheet-dialog modal-datos-dialog">

        <button
            class="auth-close"
            type="button"
            id="cerrarTratamientoDatos"
            aria-label="Cerrar ventana"
        >
            &times;
        </button>

        <p class="eyebrow">CARPIN ART</p>

        <h2 id="tituloTratamientoDatos">
            Tratamiento de datos
        </h2>

        <p class="sheet-lead">
            El presente proyecto tiene fines exclusivamente académicos.
            Los datos utilizados durante el desarrollo, las pruebas y la
            demostración del sistema son ficticios y fueron creados
            únicamente con fines de simulación. El sistema no recolecta,
            almacena ni procesa datos personales reales de estudiantes,
            docentes o terceros.
        </p>

        <button
            type="button"
            class="btn btn-dark"
            id="btnAceptarTratamientoDatos"
        >
            Entendido
        </button>

    </div>
</div>

<!-- Mensajes de confirmación y error -->
<?php if ($errorFlash || $successFlash): ?>

    <div class="flash-wrap">

        <?php if ($errorFlash): ?>
            <div class="flash flash-error">
                <?= e($errorFlash); ?>
            </div>
        <?php endif; ?>

        <?php if ($successFlash): ?>
            <div class="flash flash-ok">
                <?= e($successFlash); ?>
            </div>
        <?php endif; ?>

    </div>

<?php endif; ?>

<!-- Modal de inicio de sesión y registro -->
<div class="auth-modal" id="authModal" hidden>

    <div class="auth-dialog">

        <button
            class="auth-close"
            type="button"
            data-close-auth
            aria-label="Cerrar"
        >
            &times;
        </button>

        <div class="auth-tabs">

            <button
                type="button"
                class="is-active"
                data-auth-tab="login"
            >
                Iniciar sesión
            </button>

            <button
                type="button"
                data-auth-tab="register"
            >
                Crear cuenta
            </button>

        </div>

        <form
            class="auth-form is-active"
            id="formLogin"
            action="<?= e(url('login')); ?>"
            method="POST"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken); ?>"
            >

            <input
                type="hidden"
                name="redirect"
                value="<?= e($currentUrl); ?>"
            >

            <label>
                Correo
                <input
                    class="field"
                    type="email"
                    name="email"
                    required
                >
            </label>

            <label>
                Contraseña
                <input
                    class="field"
                    type="password"
                    name="password"
                    required
                >
            </label>

            <button class="btn btn-dark" type="submit">
                Entrar
            </button>

        </form>

        <form
            class="auth-form"
            id="formRegister"
            action="<?= e(url('registro')); ?>"
            method="POST"
            hidden
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken); ?>"
            >

            <input
                type="hidden"
                name="redirect"
                value="<?= e($currentUrl); ?>"
            >

            <div class="form-row">

                <label>
                    Nombre
                    <input
                        class="field"
                        type="text"
                        name="nombre"
                        required
                    >
                </label>

                <label>
                    Apellido
                    <input
                        class="field"
                        type="text"
                        name="apellido"
                        required
                    >
                </label>

            </div>

            <label>
                Correo
                <input
                    class="field"
                    type="email"
                    name="email"
                    required
                >
            </label>

            <label>
                Celular
                <input
                    class="field"
                    type="text"
                    name="telefono"
                >
            </label>

            <label>
                Contraseña
                <input
                    class="field"
                    type="password"
                    name="password"
                    minlength="8"
                    required
                >
            </label>

            <label>
                Confirmar contraseña
                <input
                    class="field"
                    type="password"
                    name="password_confirm"
                    minlength="8"
                    required
                >
            </label>

            <button class="btn btn-dark" type="submit">
                Crear cuenta
            </button>

        </form>

    </div>
</div>

<!-- Modal de cotización a medida -->
<div class="sheet-modal" id="measureModal" hidden>

    <div class="sheet-dialog">

        <button
            class="auth-close"
            type="button"
            data-close-measure
            aria-label="Cerrar"
        >
            &times;
        </button>

        <p class="eyebrow">
            <?= $x('modales', 'medida_eyebrow', 'A medida'); ?>
        </p>

        <h2>
            <?= $x('modales', 'medida_titulo', 'Cotiza tu mueble'); ?>
        </h2>

        <p class="sheet-lead">
            <?= $x(
                'modales',
                'medida_texto',
                'Tipo, material, medidas y referencias. Si ya iniciaste sesión, completamos tus datos.'
            ); ?>
        </p>

        <form
            action="<?= e(url('a-medida')); ?>"
            method="POST"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken); ?>"
            >

            <div class="measure-grid">

                <label>
                    Tipo de mueble
                    <select class="field" name="tipo_mueble_id">
                        <option value="">Selecciona</option>

                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= (int) $t['id']; ?>">
                                <?= e($t['nombre']); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </label>

                <label>
                    Material
                    <select class="field" name="material_id">
                        <option value="">Selecciona</option>

                        <?php foreach ($materiales as $m): ?>
                            <option value="<?= (int) $m['id']; ?>">
                                <?= e($m['nombre']); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </label>

                <label>
                    Largo (cm)
                    <input class="field" type="number" step="0.1" name="largo_cm">
                </label>

                <label>
                    Ancho (cm)
                    <input class="field" type="number" step="0.1" name="ancho_cm">
                </label>

                <label>
                    Alto (cm)
                    <input class="field" type="number" step="0.1" name="alto_cm">
                </label>

                <label>
                    Color / acabado
                    <input
                        class="field"
                        type="text"
                        name="color"
                        placeholder="Nogal, blanco, negro..."
                    >
                </label>

                <label class="span-2">
                    Imágenes de referencia
                    <input
                        class="field"
                        type="file"
                        name="imagenes[]"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    >
                </label>

                <label>
                    Nombre
                    <input
                        class="field"
                        type="text"
                        name="nombre_contacto"
                        required
                        value="<?= e(trim(
                            ($usuario['nombre'] ?? '') . ' ' .
                            ($usuario['apellido'] ?? '')
                        )); ?>"
                    >
                </label>

                <label>
                    Número de contacto
                    <input
                        class="field"
                        type="text"
                        name="telefono"
                        required
                        value="<?= e($usuario['telefono'] ?? ''); ?>"
                    >
                </label>

                <label class="span-2">
                    Correo
                    <input
                        class="field"
                        type="email"
                        name="email"
                        required
                        value="<?= e($usuario['email'] ?? ''); ?>"
                    >
                </label>

                <label>
                    Presupuesto aproximado ($)
                    <input
                        class="field"
                        type="number"
                        step="0.01"
                        name="presupuesto"
                    >
                </label>

                <label class="span-2">
                    Descripción detallada
                    <textarea
                        class="field"
                        id="measureDescripcion"
                        name="descripcion"
                        rows="4"
                        required
                        placeholder="Uso, estilo, restricciones del espacio..."
                    ><?= e(
                        $productoPrefill && $productoPrefill !== '1'
                            ? 'Me interesa: ' . $productoPrefill
                            : ''
                    ); ?></textarea>
                </label>

                <label class="check span-2">
                    <input
                        type="checkbox"
                        name="quiere_whatsapp"
                        value="1"
                    >
                    <span>
                        Quiero recibir un mensaje por WhatsApp para aclarar ideas
                    </span>
                </label>

            </div>

            <button class="btn btn-dark" type="submit">
                <?= $x('modales', 'medida_btn', 'Enviar cotización'); ?>
            </button>

        </form>
    </div>
</div>

<!-- Modal de compra -->
<div class="sheet-modal" id="buyModal" hidden>

    <div class="sheet-dialog">

        <button
            class="auth-close"
            type="button"
            data-close-buy
            aria-label="Cerrar"
        >
            &times;
        </button>

        <p class="eyebrow">
            <?= $x('modales', 'compra_eyebrow', 'Catálogo'); ?>
        </p>

        <h2>
            <?= $x('modales', 'compra_titulo', 'Comprar esta pieza'); ?>
        </h2>

        <p class="sheet-lead">
            <?= $x(
                'modales',
                'compra_texto',
                'Completa tus datos. El taller te confirma disponibilidad, entrega y forma de pago. No es un cobro en línea.'
            ); ?>
        </p>

        <div class="buy-summary">

            <div class="buy-summary-photo" id="buyFoto"></div>

            <div>
                <h3 id="buyNombre">Pieza</h3>
                <p class="price" id="buyPrecioTxt">$0</p>
                <p id="buyStockTxt"></p>
            </div>

        </div>

        <form action="<?= e(url('comprar')); ?>" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken); ?>"
            >

            <input
                type="hidden"
                name="producto_id"
                id="buyProductoId"
                value=""
            >

            <div class="measure-grid">

                <label>
                    Cantidad
                    <input
                        class="field"
                        type="number"
                        name="cantidad"
                        id="buyCantidad"
                        min="1"
                        value="1"
                        required
                    >
                </label>

                <label>
                    Total
                    <input
                        class="field"
                        id="buyTotal"
                        type="text"
                        readonly
                        value="$0"
                    >
                </label>

                <label>
                    Nombre
                    <input
                        class="field"
                        type="text"
                        name="nombre_contacto"
                        required
                        value="<?= e(trim(
                            ($usuario['nombre'] ?? '') . ' ' .
                            ($usuario['apellido'] ?? '')
                        )); ?>"
                    >
                </label>

                <label>
                    Celular
                    <input
                        class="field"
                        type="text"
                        name="telefono"
                        required
                        value="<?= e($usuario['telefono'] ?? ''); ?>"
                    >
                </label>

                <label class="span-2">
                    Correo
                    <input
                        class="field"
                        type="email"
                        name="email"
                        required
                        value="<?= e($usuario['email'] ?? ''); ?>"
                    >
                </label>

                <label>
                    Ciudad
                    <input
                        class="field"
                        type="text"
                        name="ciudad"
                        placeholder="Bogotá, Medellín..."
                    >
                </label>

                <label>
                    Dirección de entrega
                    <input
                        class="field"
                        type="text"
                        name="direccion"
                        required
                        placeholder="Calle, número, barrio"
                    >
                </label>

                <label class="span-2">
                    Notas para el taller
                    <textarea
                        class="field"
                        name="notas"
                        rows="3"
                        placeholder="Color, fecha de entrega, detalles del espacio..."
                    ></textarea>
                </label>

                <label class="check span-2">
                    <input
                        type="checkbox"
                        name="quiere_whatsapp"
                        value="1"
                    >
                    <span>
                        Quiero que me confirmen por WhatsApp
                    </span>
                </label>

            </div>

            <button class="btn btn-dark" type="submit">
                <?= $x('modales', 'compra_btn', 'Enviar pedido'); ?>
            </button>

        </form>

    </div>
</div>
