# CarpinArt — documentación del proyecto

Sitio web y panel de un taller de carpintería a medida. Una sola página pública (landing) más un CMS en `/admin`.

URL local: `http://localhost/CarpinArt/`

---

## Cómo entrar

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Administrador | `admin@carpinart.local` | `Admin123!` |
| Cliente | se crea desde **Crear / Iniciar sesión** en el menú | mínimo 8 caracteres |

El admin entra por el mismo modal de login del sitio. Si el usuario tiene rol `administrador`, redirige a `/admin`. Un cliente se queda en la landing; el menú solo cambia el último ítem a **Cerrar sesión**.

---

## Stack

| Pieza | Qué se usa |
|-------|------------|
| Servidor | Apache (XAMPP) + PHP 8.1 o superior |
| Base de datos | MariaDB/MySQL, base **`carpinart_app`**, usuario `root` sin contraseña |
| Front | HTML/CSS/JS propios (`app.css`, `app.js`). Sin React ni frameworks |
| Fuentes | Google Fonts: Cormorant Garamond + Outfit |
| Correo | PHPMailer 6 (SMTP, configurable desde el admin) |
| Subidas | Archivos en `public/uploads/` (productos, hero, galería, cotizaciones) |
| Enrutado | Front controller: todo pasa por `public/index.php` + `Router.php` |

Arquitectura simple **MVC ligero**:

- `public/index.php` arranca sesión, autoload y rutas
- `controllers/` reciben GET/POST
- `models/` hablan con PDO
- `views/` pintan HTML
- `helpers/` sesión, CSRF, subidas, correo
- `config/` constantes y conexión singleton

No hay Composer para el código propio: las clases se cargan por nombre de archivo. Composer solo instala PHPMailer (`vendor/`).

---

## Cómo se sirve

1. Apache abre `c:\xampp\htdocs\CarpinArt\`
2. `.htaccess` reescribe URLs a `public/index.php?url=...` (si el archivo no existe en disco)
3. CSS, JS, imágenes y uploads se sirven directo (no pasan por PHP)
4. `index.php` de la raíz solo incluye `public/index.php` por si alguien entra a `/CarpinArt/index.php`

`BASE_URL` está en `config/config.php`. Si mueves el proyecto, cámbiala.

---

## Sitio público (landing)

Una sola página: `views/home/index.php` con cabecera y pie.

### Menú (siempre igual)

Logo | Inicio | Nosotros | Catálogo | A medida | Crear / Iniciar sesión

Con sesión de cliente, el último ítem pasa a **Cerrar sesión**. No hay “Hola, nombre” ni “Mis solicitudes”.

Los textos del menú se editan en **Admin → Landing**.

### Secciones

1. **Hero** — carrusel de fotos de fondo. La copia y los botones no están encima de la foto (la foto no tapa los clics). Primera imagen se precarga; las demás se piden al cambiar de slide.
2. **Cintillo** — materiales que desfilan (Roble, Nogal, etc.). Texto editable, separado por comas.
3. **Invitación** — título, texto, tres cifras (ej. 12+ años de oficio) y botones.
4. **Galería** — mosaico de fotos del taller. Clic abre lightbox.
5. **Catálogo** — hasta 6 productos **destacados**, con stock > 0 y activos. Botones **Comprar**, **Cotizar** y **WhatsApp**.
6. **Nosotros** — texto + recuadro de compromiso. Foto ilustrativa.
7. **Reseñas** — testimonios publicados desde el admin.
8. **Contacto** — nombre, celular, mensaje. Guarda en BD y avisa al correo del taller.
9. **Pie** — texto, horarios (Ajustes) y WhatsApp flotante.

### Ventanas (popups)

- **Login / registro** — no son páginas aparte.
- **A medida** — cotización: tipo de mueble, material, medidas, color, fotos, datos de contacto, presupuesto. POST a `/a-medida`.
- **Comprar** — pedido de una pieza del catálogo: cantidad, dirección, notas. POST a `/comprar`. No cobra en línea: el taller confirma disponibilidad y forma de pago. Descuenta stock; si el admin cancela el pedido, el stock vuelve.

Los avisos (“Sesión iniciada”, “Cuenta creada”, etc.) se ocultan solos a los 3 segundos.

`GET /a-medida` y `GET /personalizados` redirigen al home y abren el popup de cotizar.

---

## Panel admin (`/admin`)

Solo rol `administrador`.

| Menú | Qué hace |
|------|----------|
| Inicio | Contadores: productos, pedidos pendientes, cotizaciones, mensajes |
| Landing | Edita **todo el texto** de la página, fotos del hero y de la galería. Vista previa a la derecha |
| Catálogo | CRUD de productos. Foto reemplazable. Stock 0 = se oculta en el sitio, no se borra |
| Pedidos | Compras del catálogo. Cambiar estado (pendiente, confirmado, en proceso, entregado, cancelado) |
| Cotizaciones | Solicitudes a medida. Ver detalle y responder por correo |
| Mensajes | Contacto del formulario. Marcar leído |
| Reseñas | Alta / edición / baja de testimonios |
| Ajustes | WhatsApp, correo del taller, Instagram, horarios, SMTP y envío de prueba |

---

## Textos e imágenes editables

**Admin → Landing** cubre menú, hero, botones, cifras, cintillo, galería, catálogo (rótulos), nosotros, reseñas (títulos), contacto, pie y títulos de los popups.

- Nombres, precios y fotos de muebles → **Catálogo**
- Opiniones de clientes → **Reseñas**
- WhatsApp y horarios → **Ajustes**

Las fotos se guardan en:

- `public/uploads/hero/`
- `public/uploads/galeria/`
- `public/uploads/productos/`
- `public/uploads/cotizaciones/`

Formatos: JPG, PNG, WebP.

---

## Correo (PHPMailer)

Si SMTP no está configurado, el sitio **no falla**: escribe en el log y sigue. Para enviar de verdad, en **Ajustes**:

- host (ej. `smtp.gmail.com`)
- puerto `587` + TLS (o `465` + SSL)
- usuario y **contraseña de aplicación**
- remitente y nombre

El correo del taller (destinatario interno) también está ahí. `config.php` solo es respaldo si la BD no tiene valores.

Se mandan mails en: contacto, cotización (cliente + taller), respuesta a cotización, pedido (cliente + taller), prueba SMTP.

---

## Base de datos `carpinart_app`

En esta máquina InnoDB se caía al crear tablas nuevas. Por eso:

- `roles` y `usuarios` quedaron **InnoDB** (con FK)
- el resto se creó **MyISAM** (`schema_rest.sql` y tablas que el PHP arma solo: `galeria_imagenes`, `pedidos`)

No borrar otras bases de XAMPP. Ignorar restos `carpinart` / `carpinart_db` si aparecen.

### Tablas

| Tabla | Uso |
|-------|-----|
| `roles` | `administrador`, `cliente` |
| `usuarios` | Cuentas. Login por email + hash bcrypt |
| `configuracion` | Clave/valor: WhatsApp, SMTP, extras de textos de la landing |
| `pagina_secciones` | Títulos/cuerpos por sección (`hero`, `catalogo`, `nav`, etc.). `extra_json` para botones y cifras |
| `hero_slides` | Fotos del carrusel |
| `galeria_imagenes` | Mosaico del home |
| `categorias` | Categorías del catálogo |
| `productos` | Piezas. `destacado` + `stock` + `activo` controlan visibilidad |
| `producto_imagenes` | Foto(s) del producto. En pantalla se usa la más reciente |
| `materiales` | Lista del formulario a medida |
| `tipos_mueble` | Lista del formulario a medida |
| `cotizaciones` | Pedidos a medida |
| `cotizacion_imagenes` | Referencias subidas por el cliente |
| `cotizacion_respuestas` | Respuestas del admin |
| `pedidos` | Compras del catálogo |
| `mensajes_contacto` | Formulario de contacto |
| `resenas` | Testimonios |

### Scripts SQL

- `DataBase.sql` — esquema original (InnoDB) + datos semilla. Referencia / instalación en un MySQL estable.
- `schema_rest.sql` — tablas MyISAM que se usaron en este XAMPP (incluye galería y pedidos).
- `seed.sql` — contenidos de ejemplo (secciones, productos, reseñas).
- `schema_galeria.sql` — **redundante** (ya está en `schema_rest.sql`). Se puede borrar.

`pedidos` también se crea solo si falta (`Pedido` en PHP, `CREATE TABLE IF NOT EXISTS`).

---

## Rutas

| Método | Ruta | Acción |
|--------|------|--------|
| GET | `/` | Landing |
| POST | `/contacto` | Mensaje de contacto |
| GET | `/a-medida`, `/personalizados` | Redirige al home y abre cotizar |
| POST | `/a-medida` | Guarda cotización |
| POST | `/comprar` | Guarda pedido |
| POST | `/login`, `/registro` | Auth |
| GET | `/logout` | Cierra sesión |
| GET | `/admin` … | Panel (ver `public/index.php`) |

---

## Seguridad (básica)

- Contraseñas con `password_hash` (bcrypt)
- CSRF en formularios
- Sesión `httponly`, `samesite=Lax`
- El admin exige rol `administrador`
- Subidas: se valida MIME real (JPG/PNG/WebP) y se guarda con nombre aleatorio
- `e()` escapa HTML en las vistas

No es un sistema bancario: no hay pasarela de pago ni HTTPS local.

---

## Rendimiento (lo que ya está hecho)

- Logo chico (`logo-sm.png`)
- Hero: solo la primera foto al inicio
- Galería y catálogo: `loading="lazy"` y tamaños Unsplash reducidos (`media()`)
- Menos pesos de Google Fonts
- Caché de CSS/JS/imágenes en `.htaccess`
- Avisos flash se quitan a los 3 s

---

## Carpetas que sí se necesitan

```
CarpinArt/
  .htaccess                 rewrites + caché
  index.php                 atajo a public/index.php
  Router.php
  composer.json / composer.lock
  DataBase.sql, schema_rest.sql, seed.sql
  config/config.php         URL, BD, SMTP de respaldo
  config/Database.php       PDO singleton
  public/index.php          front controller
  public/.htaccess
  public/assets/css/        app.css, admin.css
  public/assets/js/         app.js, admin.js
  public/assets/img/        logo-sm.png (en uso), logo.png (original transparente)
  public/uploads/           fotos subidas desde el admin
  controllers/              Home, Auth, Admin, Cotizacion, Pedido
  models/                   Usuario, Producto, Contenido, Cotizacion, Pedido, MensajeContacto
  helpers/                  Session, Security, Uploader, Mailer
  views/layouts/            header + footer públicos
  views/home/index.php
  views/admin/              todas las pantallas del panel
  vendor/                   PHPMailer (no borrar)
```

**Imágenes del logo**

- `public/assets/img/logo-sm.png` — el que se muestra (nav, pie, admin)
- `public/assets/img/logo.png` — versión grande transparente (por si regeneras el chico)
- `public/assets/img/logo-original.png` — copia del JPEG original; no la usa el sitio, solo respaldo

---

## Archivos que no hacen falta para que el sitio corra

Estos son restos de versiones anteriores (el login y “a medida” ahora son popups; el catálogo público sale en el home). El barrido ya los eliminó si seguían en el disco:

| Archivo | Por qué sobraba |
|---------|-----------------|
| `controllers/ProductoController.php` | No hay ruta. El catálogo lo atiende `AdminController` + `HomeController` |
| `models/Material.php` | Nadie lo llama. Los materiales salen de `Cotizacion::materiales()` |
| `views/auth/login.php` | Login está en el modal de `views/layouts/header.php` |
| `views/auth/register.php` | Igual, modal de registro |
| `views/personalizados/index.php` | La cotización es el popup `#measureModal` |
| `views/cotizaciones/crear.php` | Idem |
| `views/cotizaciones/mis_cotizaciones.php` | Nunca se enlazó (regla: el cliente no tiene “mis solicitudes”) |
| `schema_galeria.sql` | Duplicado de lo que ya está en `schema_rest.sql` |

`vendor/phpmailer/.../language/*.php` (decenas de idiomas) los trae Composer. No hacen falta para español, pero **no los borres a mano**: `composer install` los vuelve a poner. Igual los `.md` de PHPMailer.

No toques `vendor/` ni otras bases de XAMPP.

---

## Instalar de cero (otro PC)

1. Copiar la carpeta a `htdocs/CarpinArt`
2. `composer install` (o copiar `vendor/` ya armado)
3. Ajustar `BASE_URL` en `config/config.php`
4. Crear la base:
   - Preferible: `DataBase.sql` + `seed.sql` si InnoDB funciona
   - En este XAMPP: primero roles/usuarios, luego `schema_rest.sql` + `seed.sql`
5. Entrar a `http://localhost/CarpinArt/` y al admin con las credenciales de arriba
6. En Ajustes: WhatsApp real, SMTP y fotos propias

---

## Qué queda pendiente de negocio (no es código)

- Fotos reales del taller (hero, galería, catálogo)
- Número de WhatsApp verdadero
- Contraseña de aplicación de Gmail (u otro SMTP)
- En producción: `BASE_URL` con dominio, HTTPS, y `display_errors` en `0`
