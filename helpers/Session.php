<?php
// helpers/Session.php

class Session {

    /**
     * Inicia la sesión PHP con parámetros seguros de Cookie si aún no ha iniciado.
     */
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Parámetros de seguridad para las cookies de sesión
            session_set_cookie_params([
                'lifetime' => 0, // Expira al cerrar el navegador
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']), // True si usa HTTPS
                'httponly' => true, // Inaccesible vía JavaScript (previene robo de cookies por XSS)
                'samesite' => 'Lax'
            ]);
            
            session_start();
        }
    }

    /**
     * Inicia sesión para un usuario guardando sus datos en $_SESSION.
     * 
     * @param array $usuario Datos del usuario traídos de la BD
     */
    public static function login(array $usuario): void {
        self::init();
        
        // Regenerar ID de sesión para prevenir ataques de Fijación de Sesión (Session Fixation)
        session_regenerate_id(true);

        $_SESSION['user_id']       = (int) $usuario['id'];
        $_SESSION['user_nombre']   = $usuario['nombre'] ?? '';
        $_SESSION['user_apellido'] = $usuario['apellido'] ?? '';
        $_SESSION['user_email']    = $usuario['email'] ?? '';
        $_SESSION['user_telefono'] = $usuario['telefono'] ?? '';
        $_SESSION['user_rol']      = $usuario['rol_nombre'] ?? 'cliente';
        $_SESSION['logged_in']     = true;
    }

    /**
     * Destruye la sesión actual (Logout).
     */
    public static function logout(): void {
        self::init();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Verifica si hay un usuario autenticado.
     * 
     * @return bool
     */
    public static function isLoggedIn(): bool {
        self::init();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Exige que el usuario esté autenticado. Si no, lo redirige al Login.
     */
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            self::setFlash('error', 'Debes iniciar sesión para acceder a esta sección.');
            header('Location: ' . url());
            exit;
        }
    }

    /**
     * Exige un rol específico (ej: 'administrador'). Si no lo tiene, deniega el acceso.
     * 
     * @param string $rolNombre
     */
    public static function requireRole(string $rolNombre): void {
        self::requireLogin();

        if (($_SESSION['user_rol'] ?? '') !== $rolNombre) {
            http_response_code(403);
            self::setFlash('error', 'No tienes permisos suficientes para realizar esta acción.');
            header('Location: ' . url());
            exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireRole('administrador');
    }

    public static function isAdmin(): bool {
        return self::isLoggedIn() && ($_SESSION['user_rol'] ?? '') === 'administrador';
    }

    public static function user(?string $campo = null) {
        self::init();
        if ($campo === null) {
            return [
                'id'       => $_SESSION['user_id'] ?? null,
                'nombre'   => $_SESSION['user_nombre'] ?? '',
                'apellido' => $_SESSION['user_apellido'] ?? '',
                'email'    => $_SESSION['user_email'] ?? '',
                'telefono' => $_SESSION['user_telefono'] ?? '',
                'rol'      => $_SESSION['user_rol'] ?? '',
            ];
        }
        return $_SESSION[$campo] ?? null;
    }

    /**
     * Define un mensaje relámpago (Flash Message) que durará solo una petición.
     * 
     * @param string $tipo 'success', 'error', 'info', 'warning'
     * @param string $mensaje
     */
    public static function setFlash(string $tipo, string $mensaje): void {
        self::init();
        $_SESSION['flash'][$tipo] = $mensaje;
    }

    /**
     * Muestra y elimina el mensaje relámpago almacenado en la sesión.
     * 
     * @param string $tipo
     * @return string|null
     */
    public static function getFlash(string $tipo): ?string {
        self::init();
        if (isset($_SESSION['flash'][$tipo])) {
            $mensaje = $_SESSION['flash'][$tipo];
            unset($_SESSION['flash'][$tipo]);
            return $mensaje;
        }
        return null;
    }
}