<?php

class Producto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function visiblesHome(int $limite = 6): array {
        $sql = "SELECT p.*,
                       (SELECT ruta FROM producto_imagenes i WHERE i.producto_id = p.id ORDER BY orden ASC, id DESC LIMIT 1) AS imagen
                FROM productos p
                WHERE p.activo = 1 AND p.stock > 0 AND p.destacado = 1
                ORDER BY p.orden ASC, p.id ASC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function todosAdmin(): array {
        $sql = "SELECT p.*, c.nombre AS categoria,
                       (SELECT ruta FROM producto_imagenes i WHERE i.producto_id = p.id ORDER BY orden ASC, id DESC LIMIT 1) AS imagen
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                ORDER BY p.orden ASC, p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscar(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $img = $this->db->prepare("SELECT * FROM producto_imagenes WHERE producto_id = :id ORDER BY orden ASC, id DESC");
        $img->execute([':id' => $id]);
        $row['imagenes'] = $img->fetchAll();
        return $row;
    }

    public function guardar(array $d, ?int $id = null): int {
        $params = [
            ':categoria_id' => $d['categoria_id'],
            ':nombre'       => $d['nombre'],
            ':slug'         => $d['slug'],
            ':descripcion'  => $d['descripcion'],
            ':precio'       => $d['precio'],
            ':stock'        => $d['stock'],
            ':destacado'    => $d['destacado'],
            ':activo'       => $d['activo'],
            ':orden'        => $d['orden'],
        ];

        if ($id) {
            $params[':id'] = $id;
            $sql = "UPDATE productos SET categoria_id=:categoria_id, nombre=:nombre, slug=:slug,
                    descripcion=:descripcion, precio=:precio, stock=:stock, destacado=:destacado,
                    activo=:activo, orden=:orden WHERE id=:id";
            $this->db->prepare($sql)->execute($params);
            return $id;
        }

        $sql = "INSERT INTO productos (categoria_id, nombre, slug, descripcion, precio, stock, destacado, activo, orden)
                VALUES (:categoria_id, :nombre, :slug, :descripcion, :precio, :stock, :destacado, :activo, :orden)";
        $this->db->prepare($sql)->execute($params);
        return (int) $this->db->lastInsertId();
    }

    public function agregarImagen(int $productoId, string $ruta, string $alt = ''): void {
        $stmt = $this->db->prepare("INSERT INTO producto_imagenes (producto_id, ruta, alt, orden) VALUES (:pid, :ruta, :alt, 1)");
        $stmt->execute([':pid' => $productoId, ':ruta' => $ruta, ':alt' => $alt]);
    }

    public function reemplazarImagen(int $productoId, string $ruta, string $alt = ''): void {
        $stmt = $this->db->prepare("SELECT id, ruta FROM producto_imagenes WHERE producto_id = :pid ORDER BY orden ASC, id ASC");
        $stmt->execute([':pid' => $productoId]);
        $rows = $stmt->fetchAll();

        if (!$rows) {
            $this->agregarImagen($productoId, $ruta, $alt);
            return;
        }

        $keepId = (int) $rows[count($rows) - 1]['id'];
        $this->db->prepare("UPDATE producto_imagenes SET ruta = :ruta, alt = :alt, orden = 1 WHERE id = :id")
            ->execute([':ruta' => $ruta, ':alt' => $alt, ':id' => $keepId]);
        $this->db->prepare("DELETE FROM producto_imagenes WHERE producto_id = :pid AND id <> :id")
            ->execute([':pid' => $productoId, ':id' => $keepId]);

        foreach ($rows as $row) {
            if (($row['ruta'] ?? '') !== $ruta) {
                Uploader::borrar($row['ruta']);
            }
        }
    }

    public function eliminar(int $id): void {
        $this->db->prepare("DELETE FROM productos WHERE id = :id")->execute([':id' => $id]);
    }

    public function ajustarStock(int $id, int $delta): void {
        $stmt = $this->db->prepare("UPDATE productos SET stock = GREATEST(0, stock + :d) WHERE id = :id");
        $stmt->bindValue(':d', $delta, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function categorias(): array {
        return $this->db->query("SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre")->fetchAll();
    }

    public static function slug(string $nombre): string {
        $slug = strtolower(trim($nombre));
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug) ?: $slug;
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-') . '-' . substr(uniqid(), -4);
    }
}
