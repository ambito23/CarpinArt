<?php
// helpers/Security.php

class Security {

    /**
     * Genera o recupera un token CSRF único para la sesión actual.
     * 
     * @return string Token CSRF en formato hexadecimal
     */
    public static function generateCSRFToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Valida si el token enviado por el formulario coincide con el de la sesión.
     * 
     * @param string|null $token Token enviado vía POST/GET
     * @return bool
     */
    public static function validateCSRFToken(?string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        // hash_equals previene ataques de temporización (timing attacks)
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Limpia y sanitiza cadenas de texto para prevenir XSS al renderizar en vistas HTML.
     * 
     * @param string $data Texto de entrada
     * @return string Texto sanitizado
     */
    public static function sanitizeString(string $data): string {
        $data = trim($data);
        $data = strip_tags($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitiza valores de correos electrónicos.
     * 
     * @param string $email
     * @return string|false
     */
    public static function sanitizeEmail(string $email) {
        $cleanEmail = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($cleanEmail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Genera un hash seguro para contraseñas usando BCRYPT.
     * 
     * @param string $password
     * @return string Hash resultante
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verifica si una contraseña coincide con su hash almacenado en la base de datos.
     * 
     * @param string $password Contraseña ingresada por el usuario
     * @param string $hash Hash recuperado de MySQL
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}