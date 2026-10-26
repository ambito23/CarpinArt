-- CarpinArt — una sola base, 3FN
CREATE DATABASE IF NOT EXISTS carpinart_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE carpinart_app;

CREATE TABLE IF NOT EXISTS roles (
    id          TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(30)  NOT NULL,
    descripcion VARCHAR(120) NULL,
    CONSTRAINT uk_roles_nombre UNIQUE (nombre)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id         TINYINT UNSIGNED NOT NULL,
    nombre         VARCHAR(80)  NOT NULL,
    apellido       VARCHAR(80)  NOT NULL,
    email          VARCHAR(120) NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    telefono       VARCHAR(20)  NULL,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uk_usuarios_email UNIQUE (email),
    INDEX idx_usuarios_rol_id (rol_id),
    INDEX idx_usuarios_activo (activo),
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id) REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS configuracion (
    id     SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave  VARCHAR(60)  NOT NULL,
    valor  TEXT         NULL,
    CONSTRAINT uk_configuracion_clave UNIQUE (clave)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pagina_secciones (
    id            SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seccion_clave VARCHAR(60)  NOT NULL,
    titulo        VARCHAR(180) NULL,
    subtitulo     VARCHAR(255) NULL,
    cuerpo        TEXT         NULL,
    extra_json    TEXT         NULL,
    updated_at    TIMESTAMP    NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uk_secciones_clave UNIQUE (seccion_clave)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hero_slides (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo     VARCHAR(180) NULL,
    subtitulo  VARCHAR(255) NULL,
    imagen     VARCHAR(255) NOT NULL,
    orden      SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    activo     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
    id     TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    slug   VARCHAR(80) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT uk_categorias_slug UNIQUE (slug)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS materiales (
    id          SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL,
    descripcion VARCHAR(180) NULL,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT uk_materiales_nombre UNIQUE (nombre)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tipos_mueble (
    id     SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT uk_tipos_mueble_nombre UNIQUE (nombre)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id TINYINT UNSIGNED NOT NULL,
    nombre       VARCHAR(120) NOT NULL,
    slug         VARCHAR(140) NOT NULL,
    descripcion  TEXT         NULL,
    precio       DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock        INT NOT NULL DEFAULT 0,
    destacado    TINYINT(1) NOT NULL DEFAULT 0,
    activo       TINYINT(1) NOT NULL DEFAULT 1,
    orden        SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uk_productos_slug UNIQUE (slug),
    INDEX idx_productos_visible (activo, stock, destacado),
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS producto_imagenes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id INT UNSIGNED NOT NULL,
    ruta        VARCHAR(255) NOT NULL,
    alt         VARCHAR(140) NULL,
    orden       SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT fk_prodimg_producto
        FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS resenas (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_autor  VARCHAR(80) NOT NULL,
    texto         VARCHAR(400) NOT NULL,
    calificacion  TINYINT UNSIGNED NOT NULL DEFAULT 5,
    activo        TINYINT(1) NOT NULL DEFAULT 1,
    orden         SMALLINT UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mensajes_contacto (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(80) NOT NULL,
    celular    VARCHAR(20) NOT NULL,
    mensaje    TEXT NOT NULL,
    leido      TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cotizaciones (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT UNSIGNED NULL,
    tipo_mueble_id  SMALLINT UNSIGNED NULL,
    material_id     SMALLINT UNSIGNED NULL,
    largo_cm        DECIMAL(8,2) NULL,
    ancho_cm        DECIMAL(8,2) NULL,
    alto_cm         DECIMAL(8,2) NULL,
    color           VARCHAR(80) NULL,
    nombre_contacto VARCHAR(80) NOT NULL,
    telefono        VARCHAR(20) NOT NULL,
    email           VARCHAR(120) NOT NULL,
    presupuesto     DECIMAL(12,2) NULL,
    descripcion     TEXT NOT NULL,
    quiere_whatsapp TINYINT(1) NOT NULL DEFAULT 0,
    estado          ENUM('pendiente','en_revision','respondida','cerrada') NOT NULL DEFAULT 'pendiente',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cotizaciones_estado (estado),
    CONSTRAINT fk_cotiz_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_cotiz_tipo
        FOREIGN KEY (tipo_mueble_id) REFERENCES tipos_mueble(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_cotiz_material
        FOREIGN KEY (material_id) REFERENCES materiales(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cotizacion_imagenes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cotizacion_id INT UNSIGNED NOT NULL,
    ruta          VARCHAR(255) NOT NULL,
    CONSTRAINT fk_cotizimg_cotiz
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cotizacion_respuestas (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cotizacion_id INT UNSIGNED NOT NULL,
    admin_id      INT UNSIGNED NULL,
    mensaje       TEXT NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cotizresp_cotiz
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_cotizresp_admin
        FOREIGN KEY (admin_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (id, nombre, descripcion) VALUES
    (1, 'cliente', 'Cliente que solicita cotizaciones'),
    (2, 'administrador', 'Personal del taller')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono, activo)
VALUES (
    2, 'Admin', 'CarpinArt', 'admin@carpinart.local',
    '$2y$10$KFuQz2hzLSddn1TfsastaucEeWOb3TYDwcaJomS1wVHqQfNVuq946',
    '3000000000', 1
)
ON DUPLICATE KEY UPDATE email = email;

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
    ('contacto', 'Hablemos de tu proyecto', 'Contacto', 'Déjanos tu nombre, celular y un mensaje. Te escribimos pronto.')
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
