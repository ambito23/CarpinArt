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
            ':stock'        => max(0, (int) $d['stock']),
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

    /** Reserva unidades de forma atómica. Devuelve false si no hay stock suficiente. */
    public function reservarStock(int $id, int $cantidad): bool {
        $cantidad = max(1, $cantidad);
        $stmt = $this->db->prepare(
            "UPDATE productos SET stock = stock - :c
             WHERE id = :id AND stock >= :c2"
        );
        $stmt->bindValue(':c', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':c2', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /** Devuelve unidades al inventario (p. ej. pedido cancelado). Nunca deja stock negativo. */
    public function devolverStock(int $id, int $cantidad): void {
        $cantidad = max(0, $cantidad);
        if ($cantidad === 0) {
            return;
        }
        $stmt = $this->db->prepare(
            "UPDATE productos SET stock = stock + :c WHERE id = :id"
        );
        $stmt->bindValue(':c', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** @deprecated Preferir reservarStock / devolverStock */
    public function ajustarStock(int $id, int $delta): void {
        if ($delta < 0) {
            $this->reservarStock($id, abs($delta));
            return;
        }
        if ($delta > 0) {
            $this->devolverStock($id, $delta);
        }
    }

    public function corregirStockNegativo(): int {
        return (int) $this->db->exec("UPDATE productos SET stock = 0 WHERE stock < 0");
    }

    public function conStockBajo(int $umbral = 2): array {
        $umbral = max(0, $umbral);
        $stmt = $this->db->prepare(
            "SELECT id, nombre, stock, activo
             FROM productos
             WHERE stock <= :u
             ORDER BY stock ASC, nombre ASC"
        );
        $stmt->bindValue(':u', $umbral, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
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
