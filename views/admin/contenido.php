<?php
$s = fn($k, $f) => e($secciones[$k][$f] ?? '');
$sx = fn($k, $f) => e($secciones[$k]['_extra'][$f] ?? '');
?>
<section>
    <h1>Editor de la landing</h1>
    <p>Los textos se ven a la derecha mientras escribes. Guarda para actualizar fotos y el sitio público.</p>
</section>
<div class="preview-layout">
    <form class="admin-card" method="POST" action="<?= e(url('admin/contenido/guardar')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">

        <h2>Menú</h2>
        <div class="grid-2">
            <label>Inicio<input class="field" name="nav_inicio" value="<?= $sx('nav', 'inicio'); ?>" placeholder="Inicio"></label>
            <label>Nosotros<input class="field" name="nav_nosotros" value="<?= $sx('nav', 'nosotros'); ?>" placeholder="Nosotros"></label>
            <label>Catálogo<input class="field" name="nav_catalogo" value="<?= $sx('nav', 'catalogo'); ?>" placeholder="Catálogo"></label>
            <label>A medida<input class="field" name="nav_medida" value="<?= $sx('nav', 'medida'); ?>" placeholder="A medida"></label>
            <label>Crear / Iniciar sesión<input class="field" name="nav_login" value="<?= $sx('nav', 'login'); ?>" placeholder="Crear / Iniciar sesión"></label>
            <label>Cerrar sesión<input class="field" name="nav_logout" value="<?= $sx('nav', 'logout'); ?>" placeholder="Cerrar sesión"></label>
        </div>

        <h2>Hero</h2>
        <label>Título general<input class="field" name="hero_titulo" data-live="#pv-hero-title" value="<?= $s('hero', 'titulo'); ?>"></label>
        <label>Subtítulo<input class="field" name="hero_subtitulo" data-live="#pv-hero-sub" value="<?= $s('hero', 'subtitulo'); ?>"></label>
        <label>Descripción<textarea class="field" name="hero_cuerpo" data-live="#pv-hero-body" rows="3"><?= $s('hero', 'cuerpo'); ?></textarea></label>
        <div class="grid-2">
            <label>Botón cotizar<input class="field" name="hero_btn_cotizar" value="<?= $sx('hero', 'btn_cotizar'); ?>" placeholder="Cotizar a medida"></label>
            <label>Botón catálogo<input class="field" name="hero_btn_catalogo" value="<?= $sx('hero', 'btn_catalogo'); ?>" placeholder="Ver catálogo"></label>
        </div>

        <h3>Fotos del carrusel</h3>
        <?php foreach ($slides as $i => $slide): ?>
            <div class="admin-card" style="box-shadow:none;border:1px solid #eee">
                <input type="hidden" name="slide_id[]" value="<?= (int) $slide['id']; ?>">
                <input type="hidden" name="slide_imagen[]" value="<?= e($slide['imagen']); ?>">
                <label>Título slide<input class="field" name="slide_titulo[]" value="<?= e($slide['titulo']); ?>"></label>
                <label>Texto<input class="field" name="slide_subtitulo[]" value="<?= e($slide['subtitulo']); ?>"></label>
                <label>Orden<input class="field" name="slide_orden[]" type="number" value="<?= (int) $slide['orden']; ?>"></label>
                <label><input type="checkbox" name="slide_activo[<?= $i; ?>]" <?= $slide['activo'] ? 'checked' : ''; ?>> Activo</label>
                <label>Reemplazar foto<input class="field" type="file" name="slide_file[]" accept="image/*"></label>
                <img src="<?= e(media($slide['imagen'])); ?>" alt="" style="width:100%;max-height:120px;object-fit:cover;border-radius:8px">
            </div>
        <?php endforeach; ?>

        <h2>Invitación</h2>
        <label>Título<input class="field" name="invitacion_titulo" data-live="#pv-inv-title" value="<?= $s('invitacion', 'titulo'); ?>"></label>
        <label>Subtítulo<input class="field" name="invitacion_subtitulo" value="<?= $s('invitacion', 'subtitulo'); ?>"></label>
        <label>Texto<textarea class="field" name="invitacion_cuerpo" data-live="#pv-inv-body" rows="3"><?= $s('invitacion', 'cuerpo'); ?></textarea></label>
        <h3>Cifras</h3>
        <p style="color:#6a5c52;font-size:.9rem">Los tres recuadros de “12+ años de oficio”, etc.</p>
        <div class="grid-2">
            <label>Cifra 1 (número)<input class="field" name="invitacion_stat1_num" data-live="#pv-stat1-num" value="<?= $sx('invitacion', 'stat1_num'); ?>" placeholder="12+"></label>
            <label>Cifra 1 (texto)<input class="field" name="invitacion_stat1_txt" data-live="#pv-stat1-txt" value="<?= $sx('invitacion', 'stat1_txt'); ?>" placeholder="años de oficio"></label>
            <label>Cifra 2 (número)<input class="field" name="invitacion_stat2_num" data-live="#pv-stat2-num" value="<?= $sx('invitacion', 'stat2_num'); ?>" placeholder="100%"></label>
            <label>Cifra 2 (texto)<input class="field" name="invitacion_stat2_txt" data-live="#pv-stat2-txt" value="<?= $sx('invitacion', 'stat2_txt'); ?>" placeholder="a la medida"></label>
            <label>Cifra 3 (número)<input class="field" name="invitacion_stat3_num" data-live="#pv-stat3-num" value="<?= $sx('invitacion', 'stat3_num'); ?>" placeholder="1"></label>
            <label>Cifra 3 (texto)<input class="field" name="invitacion_stat3_txt" data-live="#pv-stat3-txt" value="<?= $sx('invitacion', 'stat3_txt'); ?>" placeholder="taller, trato directo"></label>
            <label>Botón crear cuenta<input class="field" name="invitacion_btn_cuenta" value="<?= $sx('invitacion', 'btn_cuenta'); ?>" placeholder="Crear cuenta"></label>
            <label>Botón a medida<input class="field" name="invitacion_btn_medida" value="<?= $sx('invitacion', 'btn_medida'); ?>" placeholder="Pedir algo personalizado"></label>
        </div>

        <h2>Cintillo de materiales</h2>
        <label>Textos (separados por coma)<textarea class="field" name="cintillo_cuerpo" rows="2" placeholder="Roble, Nogal, Pino, MDF, Melamina, A medida"><?= $s('cintillo', 'cuerpo'); ?></textarea></label>

        <h2>Galería</h2>
        <label>Título<input class="field" name="galeria_titulo" value="<?= $s('galeria', 'titulo'); ?>"></label>
        <label>Subtítulo<input class="field" name="galeria_subtitulo" value="<?= $s('galeria', 'subtitulo'); ?>"></label>
        <p style="color:#6a5c52;font-size:.9rem">Estas fotos salen en el mosaico del home. Puedes reemplazarlas, quitarlas o agregar una nueva.</p>
        <?php
        $fotosGaleria = $galeria ?? [];
        $fotosGaleria[] = ['id' => 0, 'ruta' => '', 'alt' => '', 'orden' => count($fotosGaleria) + 1, 'activo' => 1];
        foreach ($fotosGaleria as $i => $foto):
            $esNueva = empty($foto['id']);
        ?>
            <div class="admin-card" style="box-shadow:none;border:1px solid #eee">
                <input type="hidden" name="galeria_id[]" value="<?= (int) $foto['id']; ?>">
                <input type="hidden" name="galeria_ruta[]" value="<?= e($foto['ruta']); ?>">
                <strong><?= $esNueva ? 'Agregar foto' : 'Foto ' . ((int) $foto['orden']); ?></strong>
                <?php if (!empty($foto['ruta'])): ?>
                    <img src="<?= e(media($foto['ruta'])); ?>" alt="" style="width:100%;max-height:140px;object-fit:cover;border-radius:8px;margin:.5rem 0">
                <?php endif; ?>
                <label><?= $esNueva ? 'Subir foto' : 'Reemplazar foto'; ?><input class="field" type="file" name="galeria_file[]" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"></label>
                <label>Texto alternativo<input class="field" name="galeria_alt[]" value="<?= e($foto['alt']); ?>" placeholder="Ej. Taller"></label>
                <label>Orden<input class="field" name="galeria_orden[]" type="number" value="<?= (int) $foto['orden']; ?>"></label>
                <label><input type="checkbox" name="galeria_activo[<?= $i; ?>]" <?= !empty($foto['activo']) ? 'checked' : ''; ?>> Visible en el sitio</label>
                <?php if (!$esNueva): ?>
                    <label><input type="checkbox" name="galeria_borrar[<?= $i; ?>]"> Quitar esta foto</label>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <h2>Catálogo</h2>
        <label>Título<input class="field" name="catalogo_titulo" data-live="#pv-cat-title" value="<?= $s('catalogo', 'titulo'); ?>"></label>
        <label>Subtítulo<input class="field" name="catalogo_subtitulo" value="<?= $s('catalogo', 'subtitulo'); ?>"></label>
        <label>Texto<textarea class="field" name="catalogo_cuerpo" rows="2"><?= $s('catalogo', 'cuerpo'); ?></textarea></label>
        <div class="grid-2">
            <label>Botón comprar<input class="field" name="catalogo_btn_comprar" value="<?= $sx('catalogo', 'btn_comprar'); ?>" placeholder="Comprar"></label>
            <label>Botón cotizar<input class="field" name="catalogo_btn_cotizar" value="<?= $sx('catalogo', 'btn_cotizar'); ?>" placeholder="Cotizar"></label>
            <label>Botón WhatsApp<input class="field" name="catalogo_btn_whatsapp" value="<?= $sx('catalogo', 'btn_whatsapp'); ?>" placeholder="WhatsApp"></label>
        </div>

        <h2>Nosotros</h2>
        <label>Título<input class="field" name="nosotros_titulo" data-live="#pv-nos-title" value="<?= $s('nosotros', 'titulo'); ?>"></label>
        <label>Subtítulo<input class="field" name="nosotros_subtitulo" value="<?= $s('nosotros', 'subtitulo'); ?>"></label>
        <label>Texto<textarea class="field" name="nosotros_cuerpo" data-live="#pv-nos-body" rows="4"><?= $s('nosotros', 'cuerpo'); ?></textarea></label>
        <label>Compromiso título<input class="field" name="compromiso_titulo" value="<?= $s('compromiso', 'titulo'); ?>"></label>
        <label>Compromiso texto<textarea class="field" name="compromiso_cuerpo" rows="3"><?= $s('compromiso', 'cuerpo'); ?></textarea></label>

        <h2>Reseñas</h2>
        <label>Subtítulo<input class="field" name="resenas_subtitulo" value="<?= $s('resenas', 'subtitulo'); ?>" placeholder="Clientes"></label>
        <label>Título<input class="field" name="resenas_titulo" value="<?= $s('resenas', 'titulo'); ?>" placeholder="Lo que queda después de entregar"></label>
        <p style="color:#6a5c52;font-size:.9rem">Las reseñas en sí se editan en el menú Reseñas.</p>

        <h2>Contacto</h2>
        <label>Título<input class="field" name="contacto_titulo" value="<?= $s('contacto', 'titulo'); ?>"></label>
        <label>Subtítulo<input class="field" name="contacto_subtitulo" value="<?= $s('contacto', 'subtitulo'); ?>"></label>
        <label>Texto<textarea class="field" name="contacto_cuerpo" rows="2"><?= $s('contacto', 'cuerpo'); ?></textarea></label>
        <div class="grid-2">
            <label>Etiqueta nombre<input class="field" name="contacto_label_nombre" value="<?= $sx('contacto', 'label_nombre'); ?>" placeholder="Nombre"></label>
            <label>Etiqueta celular<input class="field" name="contacto_label_celular" value="<?= $sx('contacto', 'label_celular'); ?>" placeholder="Celular"></label>
            <label>Etiqueta mensaje<input class="field" name="contacto_label_mensaje" value="<?= $sx('contacto', 'label_mensaje'); ?>" placeholder="Mensaje"></label>
            <label>Botón enviar<input class="field" name="contacto_btn_enviar" value="<?= $sx('contacto', 'btn_enviar'); ?>" placeholder="Enviar mensaje"></label>
        </div>

        <h2>Pie de página</h2>
        <label>Texto<textarea class="field" name="footer_cuerpo" rows="2" placeholder="Taller de carpintería a medida. Muebles que se quedan en la casa."><?= $s('footer', 'cuerpo'); ?></textarea></label>
        <div class="grid-2">
            <label>Columna sitio<input class="field" name="footer_col_sitio" value="<?= $sx('footer', 'col_sitio'); ?>" placeholder="Sitio"></label>
            <label>Columna taller<input class="field" name="footer_col_taller" value="<?= $sx('footer', 'col_taller'); ?>" placeholder="Taller"></label>
            <label>Copyright<input class="field" name="footer_copyright" value="<?= $sx('footer', 'copyright'); ?>" placeholder="CarpinArt"></label>
        </div>

        <h2>Ventanas (A medida y Comprar)</h2>
        <div class="grid-2">
            <label>A medida · etiqueta<input class="field" name="modales_medida_eyebrow" value="<?= $sx('modales', 'medida_eyebrow'); ?>" placeholder="A medida"></label>
            <label>A medida · título<input class="field" name="modales_medida_titulo" value="<?= $sx('modales', 'medida_titulo'); ?>" placeholder="Cotiza tu mueble"></label>
            <label class="span-2" style="grid-column:1/-1">A medida · texto<textarea class="field" name="modales_medida_texto" rows="2" placeholder="Tipo, material, medidas y referencias..."><?= $sx('modales', 'medida_texto'); ?></textarea></label>
            <label>A medida · botón<input class="field" name="modales_medida_btn" value="<?= $sx('modales', 'medida_btn'); ?>" placeholder="Enviar cotización"></label>
            <label>Comprar · etiqueta<input class="field" name="modales_compra_eyebrow" value="<?= $sx('modales', 'compra_eyebrow'); ?>" placeholder="Catálogo"></label>
            <label>Comprar · título<input class="field" name="modales_compra_titulo" value="<?= $sx('modales', 'compra_titulo'); ?>" placeholder="Comprar esta pieza"></label>
            <label class="span-2" style="grid-column:1/-1">Comprar · texto<textarea class="field" name="modales_compra_texto" rows="2"><?= $sx('modales', 'compra_texto'); ?></textarea></label>
            <label>Comprar · botón<input class="field" name="modales_compra_btn" value="<?= $sx('modales', 'compra_btn'); ?>" placeholder="Enviar pedido"></label>
        </div>

        <button class="btn" type="submit">Guardar cambios</button>
    </form>

    <aside class="preview-sticky">
        <iframe class="preview-frame" src="<?= e(url()); ?>" title="Vista previa del sitio"></iframe>
        <div class="admin-card preview-live">
            <p class="eyebrow" style="letter-spacing:.16em;text-transform:uppercase;font-size:.72rem;color:#8d5a32">Preview de textos</p>
            <h2 id="pv-hero-title" data-fallback="Hero"><?= $s('hero', 'titulo'); ?></h2>
            <p id="pv-hero-sub"><?= $s('hero', 'subtitulo'); ?></p>
            <p id="pv-hero-body"><?= $s('hero', 'cuerpo'); ?></p>
            <hr>
            <h3 id="pv-inv-title"><?= $s('invitacion', 'titulo'); ?></h3>
            <p id="pv-inv-body"><?= $s('invitacion', 'cuerpo'); ?></p>
            <p><b id="pv-stat1-num"><?= $sx('invitacion', 'stat1_num') ?: '12+'; ?></b> <span id="pv-stat1-txt"><?= $sx('invitacion', 'stat1_txt') ?: 'años de oficio'; ?></span>
            · <b id="pv-stat2-num"><?= $sx('invitacion', 'stat2_num') ?: '100%'; ?></b> <span id="pv-stat2-txt"><?= $sx('invitacion', 'stat2_txt') ?: 'a la medida'; ?></span>
            · <b id="pv-stat3-num"><?= $sx('invitacion', 'stat3_num') ?: '1'; ?></b> <span id="pv-stat3-txt"><?= $sx('invitacion', 'stat3_txt') ?: 'taller, trato directo'; ?></span></p>
            <h3 id="pv-cat-title"><?= $s('catalogo', 'titulo'); ?></h3>
            <h3 id="pv-nos-title"><?= $s('nosotros', 'titulo'); ?></h3>
            <p id="pv-nos-body"><?= $s('nosotros', 'cuerpo'); ?></p>
        </div>
    </aside>
</div>
