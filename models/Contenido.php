<?php

class Contenido {
    private PDO $db;
    private ?array $configCache = null;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function seccion(string $clave): array {
        $stmt = $this->db->prepare("SELECT * FROM pagina_secciones WHERE seccion_clave = :c");
        $stmt->execute([':c' => $clave]);
        return $stmt->fetch() ?: ['titulo' => '', 'subtitulo' => '', 'cuerpo' => ''];
    }

    public function secciones(): array {
        $rows = $this->db->query("SELECT * FROM pagina_secciones")->fetchAll();
        $config = $this->config();
        $map = [];
        foreach ($rows as $row) {
            $clave = $row['seccion_clave'];
            $row['_extra'] = $this->decodificarExtras($row['extra_json'] ?? '', $config['extra_' . $clave] ?? '');
            $map[$clave] = $row;
        }
        foreach ($config as $clave => $valor) {
            if (!str_starts_with($clave, 'extra_')) {
                continue;
            }
            $secKey = substr($clave, 6);
            if (isset($map[$secKey])) {
                continue;
            }
            $decoded = json_decode((string) $valor, true);
            $map[$secKey] = [
                'titulo'    => '',
                'subtitulo' => '',
                'cuerpo'    => $config[$secKey . '_cuerpo'] ?? '',
                '_extra'    => is_array($decoded) ? $decoded : [],
            ];
        }
        return $map;
    }

    public function guardarSeccion(string $clave, string $titulo, string $subtitulo, string $cuerpo, array $extra = []): void {
        $json = $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : '';
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO pagina_secciones (seccion_clave, titulo, subtitulo, cuerpo, extra_json)
                 VALUES (:c, :t, :s, :b, :x)
                 ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), subtitulo = VALUES(subtitulo),
                    cuerpo = VALUES(cuerpo), extra_json = VALUES(extra_json)"
            );
            $stmt->execute([':c' => $clave, ':t' => $titulo, ':s' => $subtitulo, ':b' => $cuerpo, ':x' => $json ?: null]);
        } catch (PDOException $e) {
            $stmt = $this->db->prepare(
                "INSERT INTO pagina_secciones (seccion_clave, titulo, subtitulo, cuerpo)
                 VALUES (:c, :t, :s, :b)
                 ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), subtitulo = VALUES(subtitulo), cuerpo = VALUES(cuerpo)"
            );
            $stmt->execute([':c' => $clave, ':t' => $titulo, ':s' => $subtitulo, ':b' => $cuerpo]);
        }
        $this->guardarConfig('extra_' . $clave, $json);
    }

    private function decodificarExtras(string $jsonColumna, string $jsonConfig): array {
        foreach ([$jsonColumna, $jsonConfig] as $raw) {
            if ($raw === '') {
                continue;
            }
            $data = json_decode($raw, true);
            if (is_array($data) && $data) {
                return $data;
            }
        }
        return [];
    }

    public function slides(bool $soloActivos = true): array {
        $sql = "SELECT * FROM hero_slides";
        if ($soloActivos) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY orden ASC, id ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function guardarSlide(array $datos, ?int $id = null): int {
        if ($id) {
            $sql = "UPDATE hero_slides SET titulo=:titulo, subtitulo=:subtitulo, imagen=:imagen, orden=:orden, activo=:activo WHERE id=:id";
            $datos[':id'] = $id;
            $this->db->prepare($sql)->execute($datos);
            return $id;
        }
        $sql = "INSERT INTO hero_slides (titulo, subtitulo, imagen, orden, activo)
                VALUES (:titulo, :subtitulo, :imagen, :orden, :activo)";
        $this->db->prepare($sql)->execute($datos);
        return (int) $this->db->lastInsertId();
    }

    public function eliminarSlide(int $id): void {
        $this->db->prepare("DELETE FROM hero_slides WHERE id = :id")->execute([':id' => $id]);
    }

    public function galeria(bool $soloActivas = true): array {
        try {
            $this->asegurarGaleriaPorDefecto();
            $sql = "SELECT * FROM galeria_imagenes";
            if ($soloActivas) {
                $sql .= " WHERE activo = 1";
            }
            $sql .= " ORDER BY orden ASC, id ASC";
            return $this->db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    private function asegurarGaleriaPorDefecto(): void {
        $n = (int) $this->db->query("SELECT COUNT(*) FROM galeria_imagenes")->fetchColumn();
        if ($n > 0) {
            return;
        }
        $fotos = [
            ['https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1200&q=80', 'Taller'],
            ['https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80', 'Interior en madera'],
            ['https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80', 'Espacio terminado'],
            ['https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1200&q=80', 'Oficio'],
            ['https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=80', 'Sala'],
            ['https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80', 'Cocina'],
            ['https://images.unsplash.com/photo-1618220179428-22790b461013?auto=format&fit=crop&w=1200&q=80', 'Detalle'],
            ['https://images.unsplash.com/photo-1631679706909-1844bbd07221?auto=format&fit=crop&w=1200&q=80', 'Ambiente'],
        ];
        $stmt = $this->db->prepare("INSERT INTO galeria_imagenes (ruta, alt, orden, activo) VALUES (:ruta, :alt, :orden, 1)");
        foreach ($fotos as $i => $foto) {
            $stmt->execute([':ruta' => $foto[0], ':alt' => $foto[1], ':orden' => $i + 1]);
        }
    }

    public function guardarGaleriaItem(array $datos, ?int $id = null): int {
        if ($id) {
            $sql = "UPDATE galeria_imagenes SET ruta=:ruta, alt=:alt, orden=:orden, activo=:activo WHERE id=:id";
            $datos[':id'] = $id;
            $this->db->prepare($sql)->execute($datos);
            return $id;
        }
        $sql = "INSERT INTO galeria_imagenes (ruta, alt, orden, activo) VALUES (:ruta, :alt, :orden, :activo)";
        $this->db->prepare($sql)->execute($datos);
        return (int) $this->db->lastInsertId();
    }

    public function eliminarGaleriaItem(int $id): void {
        $stmt = $this->db->prepare("SELECT ruta FROM galeria_imagenes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $ruta = $stmt->fetchColumn();
        Uploader::borrar($ruta ?: null);
        $this->db->prepare("DELETE FROM galeria_imagenes WHERE id = :id")->execute([':id' => $id]);
    }

    public function resenas(bool $soloActivas = true): array {
        $sql = "SELECT * FROM resenas";
        if ($soloActivas) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY orden ASC, id ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function guardarResena(array $datos, ?int $id = null): void {
        if ($id) {
            $datos[':id'] = $id;
            $this->db->prepare(
                "UPDATE resenas SET nombre_autor=:nombre_autor, texto=:texto, calificacion=:calificacion, activo=:activo, orden=:orden WHERE id=:id"
            )->execute($datos);
            return;
        }
        $this->db->prepare(
            "INSERT INTO resenas (nombre_autor, texto, calificacion, activo, orden)
             VALUES (:nombre_autor, :texto, :calificacion, :activo, :orden)"
        )->execute($datos);
    }

    public function eliminarResena(int $id): void {
        $this->db->prepare("DELETE FROM resenas WHERE id = :id")->execute([':id' => $id]);
    }

    public function config(?string $clave = null) {
        if ($this->configCache === null) {
            $rows = $this->db->query("SELECT clave, valor FROM configuracion")->fetchAll();
            $map = [];
            foreach ($rows as $row) {
                $map[$row['clave']] = $row['valor'];
            }
            $this->configCache = $map;
        }
        if ($clave === null) {
            return $this->configCache;
        }
        return $this->configCache[$clave] ?? '';
    }

    public function guardarConfig(string $clave, string $valor): void {
        $stmt = $this->db->prepare(
            "INSERT INTO configuracion (clave, valor) VALUES (:c, :v)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)"
        );
        $stmt->execute([':c' => $clave, ':v' => $valor]);
        if ($this->configCache !== null) {
            $this->configCache[$clave] = $valor;
        }
    }
}
