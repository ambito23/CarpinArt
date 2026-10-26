<?php

class Uploader {
    public static function guardar(array $file, string $carpeta, array $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']): ?string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        $mime = $info['mime'] ?? (@mime_content_type($file['tmp_name']) ?: ($file['type'] ?? ''));
        if ($mime === 'image/jpg') {
            $mime = 'image/jpeg';
        }
        if (!in_array($mime, $allowed, true)) {
            return null;
        }

        $dir = UPLOAD_PATH . '/' . trim($carpeta, '/');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
        $nombre = $carpeta . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $nombre)) {
            return null;
        }

        return 'public/uploads/' . trim($carpeta, '/') . '/' . $nombre;
    }

    public static function varios(array $files, string $carpeta): array {
        $rutas = [];
        if (!isset($files['name']) || !is_array($files['name'])) {
            $uno = self::guardar($files, $carpeta);
            return $uno ? [$uno] : [];
        }

        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
            $ruta = self::guardar($file, $carpeta);
            if ($ruta) {
                $rutas[] = $ruta;
            }
        }
        return $rutas;
    }

    public static function borrar(?string $ruta): void {
        if (!$ruta || preg_match('#^https?://#i', $ruta)) {
            return;
        }
        $full = BASE_PATH . '/' . ltrim(str_replace('\\', '/', $ruta), '/');
        $uploads = realpath(UPLOAD_PATH);
        $real = realpath($full);
        if ($uploads && $real && str_starts_with($real, $uploads) && is_file($real)) {
            @unlink($real);
        }
    }

    public static function archivo(array $files, string $campo, int $indice): ?array {
        if (empty($files[$campo]['name'][$indice])) {
            return null;
        }
        return [
            'name'     => $files[$campo]['name'][$indice],
            'type'     => $files[$campo]['type'][$indice],
            'tmp_name' => $files[$campo]['tmp_name'][$indice],
            'error'    => $files[$campo]['error'][$indice],
            'size'     => $files[$campo]['size'][$indice],
        ];
    }
}
