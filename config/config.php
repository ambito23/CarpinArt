<?php

define('APP_NAME', 'CarpinArt');
define('BASE_PATH', dirname(__DIR__));

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        $haystack = (string) $haystack;
        $needle = (string) $needle;
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        $haystack = (string) $haystack;
        $needle = (string) $needle;
        if ($needle === '') {
            return true;
        }
        return substr($haystack, -strlen($needle)) === $needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        $haystack = (string) $haystack;
        $needle = (string) $needle;
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

function detectarBaseUrl(): string {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = rtrim(dirname($script), '/');
    if (str_ends_with($dir, '/public')) {
        $dir = substr($dir, 0, -7);
    }
    if ($dir === '/' || $dir === '\\' || $dir === '.' || $dir === '') {
        $dir = '';
    }

    return $scheme . '://' . $host . $dir . '/';
}

define('BASE_URL', detectarBaseUrl());

define('DB_HOST', 'localhost');
define('DB_NAME', 'carpinart_app');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@carpinart.local');
define('SMTP_FROM_NAME', 'CarpinArt');
define('MAIL_EMPRESA', 'carpinarttt@gmail.com');

define('UPLOAD_URL', BASE_URL . 'public/uploads/');
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');

function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function media(?string $path, string $size = 'card'): string {
    if (!$path) {
        $path = 'https://images.unsplash.com/photo-1615874959474-d609969a20d6?auto=format&fit=crop&w=900&q=80';
    } elseif (!preg_match('#^https?://#i', $path)) {
        return url($path);
    }

    if (!preg_match('#images\.unsplash\.com#i', $path)) {
        return $path;
    }

    $widths = [
        'hero'    => 1280,
        'gallery' => 640,
        'card'    => 560,
        'thumb'   => 200,
        'full'    => 1400,
    ];
    $w = $widths[$size] ?? 800;
    $base = preg_replace('/[?&](w|h|q|auto|fit|fm|crop)=[^&]*/i', '', $path);
    $base = rtrim($base, '?&');
    $sep = str_contains($base, '?') ? '&' : '?';
    return $base . $sep . 'auto=format&fit=crop&w=' . $w . '&q=68';
}