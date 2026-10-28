
<?php
$slides = $slides ?? [];
$secciones = $secciones ?? [];
$galeria = $galeria ?? [];
$productos = $productos ?? [];
$resenas = $resenas ?? [];

$first = $slides[0] ?? [
    'titulo' => 'Madera que cuenta tu historia',
    'subtitulo' => 'Creamos muebles únicos, hechos a tu medida.'
];
?>



<main>
<section class="hero" id="inicio">
    <div class="hero-track" id="heroTrack">
        <?php foreach ($slides as $i => $slide):
            $heroSrc = media($slide['imagen'] ?? '', 'hero');
        ?>
            <article
                class="hero-slide <?= $i === 0 ? 'is-active' : ''; ?>"
                style="background-image:url('<?= e($heroSrc); ?>')"
                data-title="<?= e($slide['titulo'] ?? ($secciones['hero']['titulo'] ?? 'Madera que cuenta tu historia')); ?>"
                data-sub="<?= e($slide['subtitulo'] ?? ($secciones['hero']['cuerpo'] ?? '')); ?>"
            ></article>
        <?php endforeach; ?>

        <?php if (empty($slides)): ?>
            <article class="hero-slide is-active"
                style="background:linear-gradient(90deg,rgba(35,22,14,.8),rgba(35,22,14,.2)),url('https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1800&q=85') center/cover no-repeat;">
            </article>
        <?php endif; ?>
    </div>

    <div class="hero-shade"></div>

    <div class="hero-copy">
        <p class="eyebrow">
            <?= $sec('hero', 'subtitulo', 'Taller de carpintería a medida'); ?>
        </p>

        <h1 id="heroTitle">
            <?= e($first['titulo'] ?: ($secciones['hero']['titulo'] ?? 'Madera que cuenta tu historia')); ?>
        </h1>

        <p id="heroSub">
            <?= e($first['subtitulo'] ?: ($secciones['hero']['cuerpo'] ?? 'Creamos muebles únicos, hechos a tu medida.')); ?>
        </p>

        <div class="hero-actions">
            <button class="btn btn-light" type="button" data-open-measure>
                <?= $x('hero', 'btn_cotizar', 'Cotizar a medida'); ?>
            </button>

            <a class="btn btn-ghost" href="<?= e(url('#catalogo')); ?>">
                <?= $x('hero', 'btn_catalogo', 'Ver catálogo'); ?>
            </a>
        </div>
    </div>

    <div class="hero-nav">
        <button type="button" data-hero="prev" aria-label="Anterior">‹</button>
        <div class="hero-dots" id="heroDots"></div>
        <button type="button" data-hero="next" aria-label="Siguiente">›</button>
    </div>

    <div class="hero-progress">
        <span id="heroProgress"></span>
    </div>
</section>

<div class="ticker" aria-hidden="true">
    <div class="ticker-track">
        <?php
        $cintillo = $secciones['cintillo']['cuerpo'] ?? '';
        $tickerItems = array_values(array_filter(
            array_map('trim', preg_split('/[,|\n]+/', $cintillo ?: 'Roble, Nogal, Pino, MDF, Melamina, A medida'))
        ));

        if (!$tickerItems) {
            $tickerItems = ['Roble', 'Nogal', 'Pino', 'MDF', 'Melamina', 'A medida'];
        }

        foreach (array_merge($tickerItems, $tickerItems) as $item):
        ?>
            <span><?= e($item); ?></span>
        <?php endforeach; ?>
    </div>
</div>

<section class="band invitacion reveal">
    <div class="wrap split">
        <div>
            <p class="eyebrow"><?= $sec('invitacion', 'subtitulo', 'Cotiza sin compromiso'); ?></p>
            <h2><?= $sec('invitacion', 'titulo', 'Cuéntanos el mueble que imaginas'); ?></h2>
            <p><?= $sec('invitacion', 'cuerpo', 'Crea una cuenta o escríbenos. Te respondemos con tiempo, materiales y precio.'); ?></p>

            <div class="stat-row">
                <div><b><?= $x('invitacion', 'stat1_num', '12+'); ?></b><span><?= $x('invitacion', 'stat1_txt', 'años de oficio'); ?></span></div>
                <div><b><?= $x('invitacion', 'stat2_num', '100%'); ?></b><span><?= $x('invitacion', 'stat2_txt', 'a la medida'); ?></span></div>
                <div><b><?= $x('invitacion', 'stat3_num', '1'); ?></b><span><?= $x('invitacion', 'stat3_txt', 'taller, trato directo'); ?></span></div>
            </div>
        </div>

        <div class="cta-panel">
            <?php if (!Session::isLoggedIn()): ?>
                <button class="btn btn-dark" type="button" data-open-auth="register">
                    <?= $x('invitacion', 'btn_cuenta', 'Crear cuenta'); ?>
                </button>
            <?php endif; ?>

            <button class="btn btn-line" type="button" data-open-measure>
                <?= $x('invitacion', 'btn_medida', 'Pedir algo personalizado'); ?>
            </button>
        </div>
    </div>
</section>

<section class="gallery-section" id="galeria">
    <div class="wrap">
        <header class="gallery-head reveal">
            <p class="eyebrow"><?= $sec('galeria', 'subtitulo', 'Taller'); ?></p>
            <h2><?= $sec('galeria', 'titulo', 'Así se ve el oficio'); ?></h2>
        </header>

        <div class="gallery-mosaic">
            <?php foreach ($galeria as $foto):
                $thumb = media($foto['ruta'] ?? '', 'gallery');
                $full = media($foto['ruta'] ?? '', 'full');
            ?>
                <button class="gallery-item" type="button"
                    data-gallery-src="<?= e($full); ?>"
                    data-gallery-alt="<?= e($foto['alt'] ?? 'Galería CarpinArt'); ?>">
                    <img src="<?= e($thumb); ?>"
                        alt="<?= e($foto['alt'] ?? ''); ?>"
                        loading="lazy" decoding="async" width="640" height="400">
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="lightbox" id="lightbox" hidden>
    <button class="lightbox-close" type="button" data-close-lightbox>&times;</button>
    <img id="lightboxImg" alt="">
</div>

<section class="section" id="catalogo">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow"><?= $sec('catalogo', 'subtitulo', 'Catálogo'); ?></p>
            <h2><?= $sec('catalogo', 'titulo', 'Piezas seleccionadas'); ?></h2>
            <p><?= $sec('catalogo', 'cuerpo', ''); ?></p>
        </header>

        <div class="catalog-grid">
            <?php foreach ($productos as $p):
                $foto = media($p['imagen'] ?? null, 'card');
            ?>
                <article class="product-card reveal">
                    <div class="product-photo">
                        <img src="<?= e($foto); ?>" alt="<?= e($p['nombre'] ?? 'Producto'); ?>"
                            loading="lazy" decoding="async" width="560" height="240">

                        <div class="product-hover">
                            <button class="btn btn-light btn-sm" type="button"
                                data-open-buy
                                data-buy-id="<?= (int) ($p['id'] ?? 0); ?>"
                                data-buy-name="<?= e($p['nombre'] ?? ''); ?>"
                                data-buy-price="<?= e((string) ($p['precio'] ?? 0)); ?>"
                                data-buy-stock="<?= (int) ($p['stock'] ?? 0); ?>"
                                data-buy-image="<?= e($foto); ?>">
                                <?= $x('catalogo', 'btn_comprar', 'Comprar'); ?>
                            </button>

                            <button class="btn btn-ghost btn-sm" type="button"
                                data-open-measure="<?= e($p['nombre'] ?? ''); ?>">
                                <?= $x('catalogo', 'btn_cotizar', 'Cotizar'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="product-body">
                        <h3><?= e($p['nombre'] ?? ''); ?></h3>
                        <p><?= e($p['descripcion'] ?? ''); ?></p>
                        <p class="price">$<?= number_format((float) ($p['precio'] ?? 0), 0, ',', '.'); ?></p>

                        <div class="product-actions">
                            <button class="btn btn-dark btn-sm" type="button"
                                data-open-buy
                                data-buy-id="<?= (int) ($p['id'] ?? 0); ?>"
                                data-buy-name="<?= e($p['nombre'] ?? ''); ?>"
                                data-buy-price="<?= e((string) ($p['precio'] ?? 0)); ?>"
                                data-buy-stock="<?= (int) ($p['stock'] ?? 0); ?>"
                                data-buy-image="<?= e($foto); ?>">
                                <?= $x('catalogo', 'btn_comprar', 'Comprar'); ?>
                            </button>

                            <button class="btn btn-line btn-sm" type="button"
                                data-open-measure="<?= e($p['nombre'] ?? ''); ?>">
                                <?= $x('catalogo', 'btn_cotizar', 'Cotizar'); ?>
                            </button>

                            <a class="btn btn-wa btn-sm" target="_blank" rel="noopener"
                                href="https://wa.me/<?= e($whatsapp); ?>?text=<?= rawurlencode('Hola CarpinArt, me interesa: ' . ($p['nombre'] ?? '')); ?>">
                                <?= $x('catalogo', 'btn_whatsapp', 'WhatsApp'); ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt" id="nosotros">
    <div class="wrap about-grid">
        <figure class="about-photo reveal">
            <img src="<?= e(media('https://images.unsplash.com/photo-1504148455328-c376907d081c', 'card')); ?>"
                alt="Taller de carpintería" loading="lazy" decoding="async" width="560" height="460">
        </figure>

        <div class="reveal">
            <p class="eyebrow"><?= $sec('nosotros', 'subtitulo', 'Nosotros'); ?></p>
            <h2><?= $sec('nosotros', 'titulo', 'Hecho en taller, pensado para durar'); ?></h2>
            <p class="lead"><?= $sec('nosotros', 'cuerpo', ''); ?></p>

            <div class="pledge">
                <h3><?= $sec('compromiso', 'titulo', 'Compromiso'); ?></h3>
                <p><?= $sec('compromiso', 'cuerpo', ''); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="resenas">
    <div class="wrap">
        <header class="section-head reveal">
            <p class="eyebrow"><?= $sec('resenas', 'subtitulo', 'Clientes'); ?></p>
            <h2><?= $sec('resenas', 'titulo', 'Lo que queda después de entregar'); ?></h2>
        </header>

        <div class="review-grid">
            <?php foreach ($resenas as $r): ?>
                <blockquote class="review-card reveal">
                    <p class="stars"><?= str_repeat('★', (int) ($r['calificacion'] ?? 0)); ?></p>
                    <p><?= e($r['texto'] ?? ''); ?></p>
                    <cite><?= e($r['nombre_autor'] ?? 'Cliente CarpinArt'); ?></cite>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-dark" id="contacto">
    <div class="wrap contact-grid">
        <div class="reveal">
            <p class="eyebrow"><?= $sec('contacto', 'subtitulo', 'Contacto'); ?></p>
            <h2><?= $sec('contacto', 'titulo', 'Hablemos de tu proyecto'); ?></h2>
            <p><?= $sec('contacto', 'cuerpo', ''); ?></p>
        </div>

        <form class="contact-form reveal" action="<?= e(url('contacto')); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">

            <label>
                <?= $x('contacto', 'label_nombre', 'Nombre'); ?>
                <input class="field" type="text" name="nombre" required
                    value="<?= e(Session::user('user_nombre') ?? ''); ?>">
            </label>

            <label>
                <?= $x('contacto', 'label_celular', 'Celular'); ?>
                <input class="field" type="text" name="celular" required
                    value="<?= e(Session::user('user_telefono') ?? ''); ?>">
            </label>

            <label>
                <?= $x('contacto', 'label_mensaje', 'Mensaje'); ?>
                <textarea class="field" name="mensaje" rows="4" required></textarea>
            </label>

            <button class="btn btn-light" type="submit">
                <?= $x('contacto', 'btn_enviar', 'Enviar mensaje'); ?>
            </button>
        </form>
    </div>
</section>
</main>
