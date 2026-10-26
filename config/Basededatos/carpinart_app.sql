-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2026 a las 15:29:29
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `carpinart_app`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`, `activo`) VALUES
(1, 'Sala y comedor', 'sala-comedor', 1),
(2, 'Dormitorio', 'dormitorio', 1),
(3, 'Cocina y almacenamiento', 'cocina', 1),
(4, 'Oficina', 'oficina', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `clave` varchar(60) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`) VALUES
(1, 'whatsapp_numero', '573000000000'),
(2, 'correo_empresa', 'taller@carpinart.local'),
(3, 'instagram', ''),
(4, 'horarios', 'Lun a Sáb · 8:00 a.m. – 6:00 p.m.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

CREATE TABLE `cotizaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_mueble_id` smallint(5) UNSIGNED DEFAULT NULL,
  `material_id` smallint(5) UNSIGNED DEFAULT NULL,
  `largo_cm` decimal(8,2) DEFAULT NULL,
  `ancho_cm` decimal(8,2) DEFAULT NULL,
  `alto_cm` decimal(8,2) DEFAULT NULL,
  `color` varchar(80) DEFAULT NULL,
  `nombre_contacto` varchar(80) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(120) NOT NULL,
  `presupuesto` decimal(12,2) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `quiere_whatsapp` tinyint(1) NOT NULL DEFAULT 0,
  `estado` enum('pendiente','en_revision','respondida','cerrada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id`, `usuario_id`, `tipo_mueble_id`, `material_id`, `largo_cm`, `ancho_cm`, `alto_cm`, `color`, `nombre_contacto`, `telefono`, `email`, `presupuesto`, `descripcion`, `quiere_whatsapp`, `estado`, `created_at`, `updated_at`) VALUES
(1, 3, 6, 5, -0.40, 0.50, 0.50, 'negro', 'Angie garcia', '3207940107', 'angienicolgarciarocha@gmail.com', 10.00, 'fewgdrh', 1, 'respondida', '2026-08-31 13:23:29', '2026-08-31 13:24:00'),
(2, 3, 1, 2, 0.40, 0.50, 0.30, 'u8y', 'Angie garcia', '3207940107', 'angienicolgarciarocha@gmail.com', NULL, 'Me interesa: Cabecero tapizado Roble', 0, 'pendiente', '2026-08-31 14:09:16', NULL),
(3, 4, 6, 1, 1.90, 1.90, 10.00, 'Nogal negro', 'pepe perez', '320987560', 'pepe23@gmail.com', 400.00, 'Que tenga estrellitas y alumbre', 1, 'pendiente', '2026-10-14 14:06:02', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_imagenes`
--

CREATE TABLE `cotizacion_imagenes` (
  `id` int(10) UNSIGNED NOT NULL,
  `cotizacion_id` int(10) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_respuestas`
--

CREATE TABLE `cotizacion_respuestas` (
  `id` int(10) UNSIGNED NOT NULL,
  `cotizacion_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `mensaje` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizacion_respuestas`
--

INSERT INTO `cotizacion_respuestas` (`id`, `cotizacion_id`, `admin_id`, `mensaje`, `created_at`) VALUES
(1, 1, 1, 'no sea pobre como q 10 pesos', '2026-08-31 13:24:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(180) DEFAULT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `titulo`, `subtitulo`, `imagen`, `orden`, `activo`) VALUES
(1, 'Cocinas y closets que aprovechan cada centímetro', 'Carpintería a medida', 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=1920&q=80', 1, 1),
(2, 'Mesas, estanterías y piezas para el día a día', 'Diseño y oficio', 'https://images.unsplash.com/photo-1615874959474-d609969a20d6?auto=format&fit=crop&w=1920&q=80', 2, 1),
(3, 'El taller abierto a tu idea', 'Desde el primer boceto', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1920&q=80', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(180) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'Roble', 'Duro, veta marcada', 1),
(2, 'Nogal', 'Tono oscuro elegante', 1),
(3, 'Pino', 'Liviano y versátil', 1),
(4, 'MDF chapado', 'Acabado uniforme', 1),
(5, 'Melamina', 'Fácil mantenimiento', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

CREATE TABLE `mensajes_contacto` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina_secciones`
--

CREATE TABLE `pagina_secciones` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `seccion_clave` varchar(60) NOT NULL,
  `titulo` varchar(180) DEFAULT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `cuerpo` text DEFAULT NULL,
  `extra_json` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagina_secciones`
--

INSERT INTO `pagina_secciones` (`id`, `seccion_clave`, `titulo`, `subtitulo`, `cuerpo`, `extra_json`, `updated_at`) VALUES
(1, 'hero', 'Madera que cuenta tu historia', 'Taller de carpintería a medida', 'Diseñamos y fabricamos muebles únicos para tu casa o tu negocio.', NULL, NULL),
(2, 'invitacion', 'Cuéntanos el mueble que imaginas', 'Cotiza sin compromiso', 'Crea una cuenta o escríbenos. Te respondemos con tiempo, materiales y precio.', NULL, NULL),
(3, 'catalogo', 'Piezas seleccionadas', 'Catálogo', 'Seis diseños listos para pedir o adaptar a tu espacio.', NULL, NULL),
(4, 'nosotros', 'Hecho en taller, pensado para durar', 'Nosotros', 'En CarpinArt combinamos oficio tradicional con diseño contemporáneo. Cada encargo se dibuja, se corta y se ensambla con la misma atención: medidas reales, maderas honestas y un trato directo con quien va a vivir con esa pieza.', NULL, NULL),
(5, 'compromiso', 'Compromiso', NULL, 'Medidas exactas, materiales claros y fechas que se cumplen. Si algo no encaja, lo corregimos en taller antes de entregarlo.', NULL, NULL),
(6, 'contacto', 'Hablemos de tu proyecto', 'Contacto', 'Déjanos tu nombre, celular y un mensaje. Te escribimos pronto.', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `producto_nombre` varchar(120) NOT NULL,
  `cantidad` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre_contacto` varchar(80) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(120) NOT NULL,
  `ciudad` varchar(80) DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `notas` text DEFAULT NULL,
  `quiere_whatsapp` tinyint(1) NOT NULL DEFAULT 0,
  `stock_aplicado` tinyint(1) NOT NULL DEFAULT 1,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `producto_id`, `producto_nombre`, `cantidad`, `precio_unitario`, `total`, `usuario_id`, `nombre_contacto`, `telefono`, `email`, `ciudad`, `direccion`, `notas`, `quiere_whatsapp`, `stock_aplicado`, `estado`, `created_at`) VALUES
(1, 6, 'Alacena de cocina', 1, 4100000.00, 4100000.00, 3, 'Angie garcia', '3207940107', 'angienicolgarciarocha@gmail.com', 'zxcvbnm', 'dfghj', 'wsexcrtvbynu', 0, 1, 'pendiente', '2026-08-31 14:10:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria_id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `slug`, `descripcion`, `precio`, `stock`, `destacado`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mesa de comedor Nogal', 'mesa-comedor-nogal', 'Mesa para 6 puestos, tapa maciza y patas en ángulo.', 1850000.00, 4, 1, 1, 1, '2026-08-31 13:12:50', NULL),
(2, 2, 'Closet empotrado Lineal', 'closet-empotrado-lineal', 'Frentes lisos, cajones internos y barra de colgar.', 3200000.00, 2, 1, 1, 2, '2026-08-31 13:12:50', NULL),
(3, 1, 'Estantería modular Roble', 'estanteria-modular-roble', 'Módulos que crecen con la pared. Ideal para sala o estudio.', 980000.00, 6, 1, 1, 3, '2026-08-31 13:12:50', NULL),
(4, 4, 'Escritorio de trabajo', 'escritorio-trabajo', 'Cubierta amplia, pasacables y cajón con llave.', 1250000.00, 3, 1, 1, 4, '2026-08-31 13:12:50', NULL),
(5, 2, 'Cabecero tapizado Roble', 'cabecero-tapizado', 'Madera vista y panel suave. Se fabrica a la medida de la cama.', 890000.00, 5, 1, 1, 5, '2026-08-31 13:12:50', NULL),
(6, 3, 'Alacena de cocina', 'alacena-cocina', 'Módulos superiores e inferiores, herrajes de cierre suave.', 4100000.00, 0, 1, 1, 6, '2026-08-31 13:12:50', '2026-08-31 14:10:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_imagenes`
--

CREATE TABLE `producto_imagenes` (
  `id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `alt` varchar(140) DEFAULT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_imagenes`
--

INSERT INTO `producto_imagenes` (`id`, `producto_id`, `ruta`, `alt`, `orden`) VALUES
(1, 1, 'https://images.unsplash.com/photo-1604578762246-41134e37f9cc?auto=format&fit=crop&w=900&q=80', 'Mesa de comedor', 1),
(2, 2, 'public/uploads/productos/productos_1788184734_2c9d600f.png', 'Closet empotrado Lineal', 1),
(3, 3, 'https://images.unsplash.com/photo-1594026112284-02bb6f3352fe?auto=format&fit=crop&w=900&q=80', 'Estantería', 1),
(4, 4, 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?auto=format&fit=crop&w=900&q=80', 'Escritorio', 1),
(5, 5, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80', 'Cabecero', 1),
(6, 6, 'https://images.unsplash.com/photo-1556912173-46c336c7fd55?auto=format&fit=crop&w=900&q=80', 'Cocina', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre_autor` varchar(80) NOT NULL,
  `texto` varchar(400) NOT NULL,
  `calificacion` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resenas`
--

INSERT INTO `resenas` (`id`, `nombre_autor`, `texto`, `calificacion`, `activo`, `orden`) VALUES
(1, 'Laura M.', 'El closet quedó exacto al hueco. Se nota el oficio y fueron claros con los tiempos.', 5, 1, 1),
(2, 'Andrés P.', 'Pedí la mesa de comedor y el color del nogal es precioso. Buen trato desde el primer WhatsApp.', 5, 1, 2),
(3, 'Camila R.', 'Nos armaron la cocina por módulos. Cumplieron la fecha y dejaron todo limpio.', 4, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'cliente', 'Cliente que solicita cotizaciones'),
(2, 'administrador', 'Personal del taller');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_mueble`
--

CREATE TABLE `tipos_mueble` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_mueble`
--

INSERT INTO `tipos_mueble` (`id`, `nombre`, `activo`) VALUES
(1, 'Closet / clóset', 1),
(2, 'Cocina integral', 1),
(3, 'Mesa', 1),
(4, 'Estantería', 1),
(5, 'Escritorio', 1),
(6, 'Cama / cabecero', 1),
(7, 'Otro', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `rol_id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol_id`, `nombre`, `apellido`, `email`, `password_hash`, `telefono`, `activo`, `created_at`, `updated_at`) VALUES
(1, 2, 'Admin', 'CarpinArt', 'admin@carpinart.local', '$2y$10$KFuQz2hzLSddn1TfsastaucEeWOb3TYDwcaJomS1wVHqQfNVuq946', '3000000000', 1, '2026-08-31 13:12:49', NULL),
(3, 1, 'Angie', 'garcia', 'angienicolgarciarocha@gmail.com', '$2y$10$VpyFApX/h.pcH5JkuenOduWvZTaQj5ziSp4HXvHI6GezAybFWA/fq', NULL, 1, '2026-08-31 13:22:42', NULL),
(4, 1, 'pepe', 'perez', 'pepe23@gmail.com', '$2y$10$kqFEoqfk/3dYPiqzs4QEq.HDTYpG7D.5NGfYp0VXjbdUHq2vtAy.K', '320987560', 1, '2026-10-14 14:01:29', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_categorias_slug` (`slug`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_configuracion_clave` (`clave`);

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cotizaciones_estado` (`estado`),
  ADD KEY `fk_cotiz_usuario` (`usuario_id`),
  ADD KEY `fk_cotiz_tipo` (`tipo_mueble_id`),
  ADD KEY `fk_cotiz_material` (`material_id`);

--
-- Indices de la tabla `cotizacion_imagenes`
--
ALTER TABLE `cotizacion_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cotizimg_cotiz` (`cotizacion_id`);

--
-- Indices de la tabla `cotizacion_respuestas`
--
ALTER TABLE `cotizacion_respuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cotizresp_cotiz` (`cotizacion_id`),
  ADD KEY `fk_cotizresp_admin` (`admin_id`);

--
-- Indices de la tabla `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_materiales_nombre` (`nombre`);

--
-- Indices de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagina_secciones`
--
ALTER TABLE `pagina_secciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_secciones_clave` (`seccion_clave`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_productos_slug` (`slug`),
  ADD KEY `idx_productos_visible` (`activo`,`stock`,`destacado`),
  ADD KEY `fk_productos_categoria` (`categoria_id`);

--
-- Indices de la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prodimg_producto` (`producto_id`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_roles_nombre` (`nombre`);

--
-- Indices de la tabla `tipos_mueble`
--
ALTER TABLE `tipos_mueble`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tipos_mueble_nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_rol_id` (`rol_id`),
  ADD KEY `idx_usuarios_activo` (`activo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cotizacion_imagenes`
--
ALTER TABLE `cotizacion_imagenes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cotizacion_respuestas`
--
ALTER TABLE `cotizacion_respuestas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagina_secciones`
--
ALTER TABLE `pagina_secciones`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipos_mueble`
--
ALTER TABLE `tipos_mueble`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `fk_cotiz_material` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cotiz_tipo` FOREIGN KEY (`tipo_mueble_id`) REFERENCES `tipos_mueble` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cotiz_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cotizacion_imagenes`
--
ALTER TABLE `cotizacion_imagenes`
  ADD CONSTRAINT `fk_cotizimg_cotiz` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cotizacion_respuestas`
--
ALTER TABLE `cotizacion_respuestas`
  ADD CONSTRAINT `fk_cotizresp_admin` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cotizresp_cotiz` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  ADD CONSTRAINT `fk_prodimg_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
