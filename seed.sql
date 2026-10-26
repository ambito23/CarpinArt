USE carpinart_app;

INSERT INTO configuracion (clave, valor) VALUES
    ('whatsapp_numero', '573000000000'),
    ('correo_empresa', 'taller@carpinart.local'),
    ('instagram', ''),
    ('horarios', 'Lun a Sáb · 8:00 a.m. – 6:00 p.m.')
ON DUPLICATE KEY UPDATE clave = clave;

INSERT INTO pagina_secciones (seccion_clave, titulo, subtitulo, cuerpo) VALUES
    ('hero', 'Madera que cuenta tu historia', 'Taller de carpintería a medida', 'Diseñamos y fabricamos muebles únicos para tu casa o tu negocio.'),
    ('invitacion', 'Cuéntanos el mueble que imaginas', 'Cotiza sin compromiso', 'Crea una cuenta o escríbenos. Te respondemos con tiempo, materiales y precio.'),
    ('catalogo', 'Piezas seleccionadas', 'Catálogo', 'Seis diseños listos para pedir o adaptar a tu espacio.'),
    ('nosotros', 'Hecho en taller, pensado para durar', 'Nosotros', 'En CarpinArt combinamos oficio tradicional con diseño contemporáneo. Cada encargo se dibuja, se corta y se ensambla con la misma atención: medidas reales, maderas honestas y un trato directo con quien va a vivir con esa pieza.'),
    ('compromiso', 'Compromiso', NULL, 'Medidas exactas, materiales claros y fechas que se cumplen. Si algo no encaja, lo corregimos en taller antes de entregarlo.'),
    ('contacto', 'Hablemos de tu proyecto', 'Contacto', 'Déjanos tu nombre, celular y un mensaje. Te escribimos pronto.'),
    ('galeria', 'Así se ve el oficio', 'Taller', '')
ON DUPLICATE KEY UPDATE seccion_clave = seccion_clave;

INSERT INTO hero_slides (id, titulo, subtitulo, imagen, orden, activo) VALUES
    (1, 'Cocinas y closets que aprovechan cada centímetro', 'Carpintería a medida', 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=1920&q=80', 1, 1),
    (2, 'Mesas, estanterías y piezas para el día a día', 'Diseño y oficio', 'https://images.unsplash.com/photo-1615874959474-d609969a20d6?auto=format&fit=crop&w=1920&q=80', 2, 1),
    (3, 'El taller abierto a tu idea', 'Desde el primer boceto', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1920&q=80', 3, 1)
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);

INSERT INTO categorias (id, nombre, slug, activo) VALUES
    (1, 'Sala y comedor', 'sala-comedor', 1),
    (2, 'Dormitorio', 'dormitorio', 1),
    (3, 'Cocina y almacenamiento', 'cocina', 1),
    (4, 'Oficina', 'oficina', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO materiales (id, nombre, descripcion, activo) VALUES
    (1, 'Roble', 'Duro, veta marcada', 1),
    (2, 'Nogal', 'Tono oscuro elegante', 1),
    (3, 'Pino', 'Liviano y versátil', 1),
    (4, 'MDF chapado', 'Acabado uniforme', 1),
    (5, 'Melamina', 'Fácil mantenimiento', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO tipos_mueble (id, nombre, activo) VALUES
    (1, 'Closet / clóset', 1),
    (2, 'Cocina integral', 1),
    (3, 'Mesa', 1),
    (4, 'Estantería', 1),
    (5, 'Escritorio', 1),
    (6, 'Cama / cabecero', 1),
    (7, 'Otro', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO productos (id, categoria_id, nombre, slug, descripcion, precio, stock, destacado, activo, orden) VALUES
    (1, 1, 'Mesa de comedor Nogal', 'mesa-comedor-nogal', 'Mesa para 6 puestos, tapa maciza y patas en ángulo.', 1850000, 4, 1, 1, 1),
    (2, 2, 'Closet empotrado Lineal', 'closet-empotrado-lineal', 'Frentes lisos, cajones internos y barra de colgar.', 3200000, 2, 1, 1, 2),
    (3, 1, 'Estantería modular Roble', 'estanteria-modular-roble', 'Módulos que crecen con la pared. Ideal para sala o estudio.', 980000, 6, 1, 1, 3),
    (4, 4, 'Escritorio de trabajo', 'escritorio-trabajo', 'Cubierta amplia, pasacables y cajón con llave.', 1250000, 3, 1, 1, 4),
    (5, 2, 'Cabecero tapizado Roble', 'cabecero-tapizado', 'Madera vista y panel suave. Se fabrica a la medida de la cama.', 890000, 5, 1, 1, 5),
    (6, 3, 'Alacena de cocina', 'alacena-cocina', 'Módulos superiores e inferiores, herrajes de cierre suave.', 4100000, 1, 1, 1, 6)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO producto_imagenes (id, producto_id, ruta, alt, orden) VALUES
    (1, 1, 'https://images.unsplash.com/photo-1604578762246-41134e37f9cc?auto=format&fit=crop&w=900&q=80', 'Mesa de comedor', 1),
    (2, 2, 'https://images.unsplash.com/photo-1558997519-83ea9252edf9?auto=format&fit=crop&w=900&q=80', 'Closet empotrado', 1),
    (3, 3, 'https://images.unsplash.com/photo-1594026112284-02bb6f3352fe?auto=format&fit=crop&w=900&q=80', 'Estantería', 1),
    (4, 4, 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?auto=format&fit=crop&w=900&q=80', 'Escritorio', 1),
    (5, 5, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80', 'Cabecero', 1),
    (6, 6, 'https://images.unsplash.com/photo-1556912173-46c336c7fd55?auto=format&fit=crop&w=900&q=80', 'Cocina', 1)
ON DUPLICATE KEY UPDATE ruta = VALUES(ruta);

INSERT INTO resenas (id, nombre_autor, texto, calificacion, activo, orden) VALUES
    (1, 'Laura M.', 'El closet quedó exacto al hueco. Se nota el oficio y fueron claros con los tiempos.', 5, 1, 1),
    (2, 'Andrés P.', 'Pedí la mesa de comedor y el color del nogal es precioso. Buen trato desde el primer WhatsApp.', 5, 1, 2),
    (3, 'Camila R.', 'Nos armaron la cocina por módulos. Cumplieron la fecha y dejaron todo limpio.', 4, 1, 3)
ON DUPLICATE KEY UPDATE texto = VALUES(texto);

INSERT INTO galeria_imagenes (id, ruta, alt, orden, activo) VALUES
    (1, 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1200&q=80', 'Taller', 1, 1),
    (2, 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80', 'Interior en madera', 2, 1),
    (3, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80', 'Espacio terminado', 3, 1),
    (4, 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1200&q=80', 'Oficio', 4, 1),
    (5, 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=80', 'Sala', 5, 1),
    (6, 'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80', 'Cocina', 6, 1),
    (7, 'https://images.unsplash.com/photo-1618220179428-22790b461013?auto=format&fit=crop&w=1200&q=80', 'Detalle', 7, 1),
    (8, 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?auto=format&fit=crop&w=1200&q=80', 'Ambiente', 8, 1)
ON DUPLICATE KEY UPDATE ruta = VALUES(ruta);
