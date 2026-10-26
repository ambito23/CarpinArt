<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public static function ajustes(): array {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $db = [];
        try {
            $db = (new Contenido())->config();
        } catch (Throwable $e) {
            $db = [];
        }

        $cache = [
            'host'      => ($db['smtp_host'] ?? '') ?: SMTP_HOST,
            'port'      => (int) (($db['smtp_port'] ?? '') ?: SMTP_PORT),
            'secure'    => ($db['smtp_secure'] ?? '') ?: SMTP_SECURE,
            'user'      => ($db['smtp_user'] ?? '') ?: SMTP_USER,
            'pass'      => ($db['smtp_pass'] ?? '') ?: SMTP_PASS,
            'from'      => ($db['smtp_from'] ?? '') ?: SMTP_FROM,
            'from_name' => ($db['smtp_from_name'] ?? '') ?: SMTP_FROM_NAME,
            'empresa'   => ($db['correo_empresa'] ?? '') ?: MAIL_EMPRESA,
        ];

        return $cache;
    }

    public static function estaConfigurado(): bool {
        $a = self::ajustes();
        return $a['user'] !== '' && $a['pass'] !== '' && $a['host'] !== '';
    }

    public static function enviar(string $para, string $nombre, string $asunto, string $html): bool {
        if (!self::estaConfigurado()) {
            error_log("[CarpinArt mail] SMTP no configurado. Para={$para} Asunto={$asunto}");
            return true;
        }

        $a = self::ajustes();
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $a['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $a['user'];
            $mail->Password   = $a['pass'];
            $mail->SMTPSecure = $a['secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $a['port'] ?: ($a['secure'] === 'ssl' ? 465 : 587);
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($a['from'] ?: $a['user'], $a['from_name'] ?: 'CarpinArt');
            $mail->addAddress($para, $nombre);
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $html;
            $mail->AltBody = strip_tags($html);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public static function confirmacionContacto(array $datos): void {
        $interno = self::envoltorio(
            'Nuevo mensaje de contacto',
            '<p><b>Nombre:</b> ' . e($datos['nombre']) . '</p><p><b>Celular:</b> ' . e($datos['celular']) . '</p><p>' . nl2br(e($datos['mensaje'])) . '</p>'
        );
        self::enviar(self::ajustes()['empresa'], 'CarpinArt', 'Nuevo contacto: ' . $datos['nombre'], $interno);
    }

    public static function confirmacionCotizacion(array $datos): void {
        $html = self::envoltorio(
            'Tu cotización está en el taller',
            '<p>Hola ' . e($datos['nombre_contacto']) . ',</p><p>Recibimos tu solicitud a medida. El taller la revisa y te responde con materiales, tiempo y precio.</p>'
        );
        self::enviar($datos['email'], $datos['nombre_contacto'], 'Cotización recibida — CarpinArt', $html);

        $interno = self::envoltorio(
            'Nueva cotización a medida',
            '<p><b>Cliente:</b> ' . e($datos['nombre_contacto']) . ' · ' . e($datos['email']) . ' · ' . e($datos['telefono']) . '</p>'
            . '<p><b>Presupuesto:</b> ' . e((string) ($datos['presupuesto'] ?? 'No indicado')) . '</p>'
            . '<p>' . nl2br(e($datos['descripcion'])) . '</p>'
        );
        self::enviar(self::ajustes()['empresa'], 'CarpinArt', 'Nueva cotización: ' . $datos['nombre_contacto'], $interno);
    }

    public static function confirmacionPedido(array $datos): void {
        $total = '$' . number_format((float) $datos['total'], 0, ',', '.');
        $html = self::envoltorio(
            'Recibimos tu pedido',
            '<p>Hola ' . e($datos['nombre_contacto']) . ',</p>'
            . '<p>Pediste <b>' . (int) $datos['cantidad'] . '</b> × ' . e($datos['producto_nombre']) . ' por <b>' . $total . '</b>.</p>'
            . '<p>Esto no es un pago en línea. El taller te confirma disponibilidad, tiempo de entrega y forma de pago.</p>'
        );
        self::enviar($datos['email'], $datos['nombre_contacto'], 'Pedido recibido — CarpinArt', $html);

        $interno = self::envoltorio(
            'Nuevo pedido del catálogo',
            '<p><b>Pedido #' . (int) $datos['id'] . '</b></p>'
            . '<p><b>Pieza:</b> ' . e($datos['producto_nombre']) . ' × ' . (int) $datos['cantidad'] . ' · ' . $total . '</p>'
            . '<p><b>Cliente:</b> ' . e($datos['nombre_contacto']) . ' · ' . e($datos['email']) . ' · ' . e($datos['telefono']) . '</p>'
            . '<p><b>Entrega:</b> ' . e((string) ($datos['ciudad'] ?? '')) . ' — ' . e($datos['direccion']) . '</p>'
            . '<p>' . nl2br(e((string) ($datos['notas'] ?? ''))) . '</p>'
        );
        self::enviar(self::ajustes()['empresa'], 'CarpinArt', 'Nuevo pedido: ' . $datos['nombre_contacto'], $interno);
    }

    public static function respuestaCotizacion(string $para, string $nombre, string $mensaje): void {
        $html = self::envoltorio(
            'Respuesta de CarpinArt',
            '<p>Hola ' . e($nombre) . ',</p><p>El taller respondió tu cotización:</p><blockquote>' . nl2br(e($mensaje)) . '</blockquote>'
        );
        self::enviar($para, $nombre, 'Respuesta a tu cotización — CarpinArt', $html);
    }

    public static function prueba(string $para): bool {
        return self::enviar(
            $para,
            'CarpinArt',
            'Prueba de correo — CarpinArt',
            self::envoltorio('SMTP funcionando', '<p>Si lees esto, el correo del taller ya está configurado.</p>')
        );
    }

    private static function envoltorio(string $titulo, string $cuerpo): string {
        return '<div style="font-family:Georgia,serif;background:#f4efe6;padding:24px">'
            . '<div style="max-width:560px;margin:auto;background:#fff;padding:28px;border-radius:12px">'
            . '<p style="letter-spacing:.2em;text-transform:uppercase;color:#8b5a2b;font-size:12px">CarpinArt</p>'
            . '<h1 style="font-size:22px;color:#1a1410">' . e($titulo) . '</h1>'
            . $cuerpo
            . '<p style="color:#6b5e54;font-size:13px;margin-top:24px">Taller de carpintería a medida</p>'
            . '</div></div>';
    }
}
