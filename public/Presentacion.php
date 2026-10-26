<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conoce a nuestro equipo | Carpin Art</title>

  <link rel="stylesheet" href="assets/css/presentacion-equipo.css">
</head>

<body>

    <header class="encabezado">
        <a href="index.php" class="marca">CARPIN <span>ART</span></a>
        <p>Creatividad, diseño y trabajo en equipo</p>
    </header>

    <main>
        <section class="portada">
            <div class="portada-texto">
                <p class="etiqueta">NUESTRO EQUIPO</p>
                <h1>Detrás de cada idea, hay un gran equipo.</h1>

                <p class="introduccion">
                    Somos un grupo de estudiantes que trabaja con creatividad,
                    dedicación y compromiso para desarrollar Carpin Art, un
                    proyecto enfocado en el diseño y la comercialización de
                    muebles, buscando ofrecer una experiencia práctica y
                    agradable para nuestros usuarios.
                </p>

                <a href="#integrantes" class="boton">Conócenos</a>
            </div>

            <div class="portada-imagen">
                <img
                    src="assets/img/equipo.jpg"
                    alt="Fotografía de las integrantes del equipo"
                    onerror="this.style.display='none'; this.parentElement.classList.add('sin-foto');"
                >
                <span class="texto-foto">Nuestro equipo Carpin Art</span>
            </div>
        </section>

        <section class="nosotras" id="integrantes">
            <p class="etiqueta">LAS PERSONAS DETRÁS DEL PROYECTO</p>
            <h2>Conoce a las integrantes</h2>

            <p class="subtitulo">
                Cada una aporta sus ideas, habilidades y esfuerzo para
                hacer realidad este proyecto.
            </p>

            <div class="tarjetas">

                <article class="tarjeta">
                    <img
                        src="assets/img/integrante1.jpg"
                        alt="Foto de la primera integrante"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="iniciales" style="display:none;">01</div>
                    <div class="contenido-tarjeta">
                        <h3>Nombre de la integrante 1</h3>
                        <p class="cargo">Diseño y creatividad</p>
                        <p>
                            Participa en el desarrollo de ideas y en la
                            creación de propuestas visuales para el proyecto.
                        </p>
                    </div>
                </article>

                <article class="tarjeta">
                    <img
                        src="assets/img/integrante2.jpg"
                        alt="Foto de la segunda integrante"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="iniciales" style="display:none;">02</div>
                    <div class="contenido-tarjeta">
                        <h3>Nombre de la integrante 2</h3>
                        <p class="cargo">Desarrollo de la página web</p>
                        <p>
                            Colabora en la organización y presentación de la
                            información para que la página sea fácil de usar.
                        </p>
                    </div>
                </article>

                <article class="tarjeta">
                    <img
                        src="assets/img/integrante3.jpg"
                        alt="Foto de la tercera integrante"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="iniciales" style="display:none;">03</div>
                    <div class="contenido-tarjeta">
                        <h3>Nombre de la integrante 3</h3>
                        <p class="cargo">Organización y contenido</p>
                        <p>
                            Apoya la planificación, la revisión de contenidos
                            y la organización de las actividades del equipo.
                        </p>
                    </div>
                </article>

            </div>
            <p class="nota">
                Puedes duplicar una tarjeta si tu equipo tiene más integrantes.
            </p>
        </section>

        <section class="despedida">
            <p class="etiqueta">NUESTRO PROPÓSITO</p>
            <h2>¡Las grandes ideas se construyen en equipo!</h2>
            <p>
                Carpin Art representa nuestro esfuerzo por combinar
                creatividad, tecnología y diseño en una propuesta que
                responda a las necesidades de nuestros usuarios.
            </p>
            <a href="index.php" class="boton boton-claro">
                Entrar a Carpin Art
            </a>
        </section>
    </main>

    <footer>
        <p>© Carpin Art · Proyecto desarrollado por nuestro equipo</p>
    </footer>

</body>
</html>