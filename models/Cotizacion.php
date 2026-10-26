
<?php

class Cotizacion {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function crear(array $datos): int {
        $sql = "INSERT INTO cotizaciones (
                    usuario_id, tipo_mueble_id, material_id, largo_cm, ancho_cm, alto_cm, color,
                    nombre_contacto, telefono, email, presupuesto, descripcion, quiere_whatsapp, estado
                ) VALUES (
                    :usuario_id, :tipo_mueble_id, :material_id, :largo_cm, :ancho_cm, :alto_cm, :color,
                    :nombre_contacto, :telefono, :email, :presupuesto, :descripcion, :quiere_whatsapp, 'pendiente'
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return (int) $this->db->lastInsertId();
    }

    public function agregarImagen(int $id, string $ruta): void {
        $stmt = $this->db->prepare("INSERT INTO cotizacion_imagenes (cotizacion_id, ruta) VALUES (:id, :ruta)");
        $stmt->execute([':id' => $id, ':ruta' => $ruta]);
    }

    public function todas(): array {
        $sql = "SELECT c.*, t.nombre AS tipo_nombre, m.nombre AS material_nombre
                FROM cotizaciones c
                LEFT JOIN tipos_mueble t ON t.id = c.tipo_mueble_id
                LEFT JOIN materiales m ON m.id = c.material_id
                ORDER BY c.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscar(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT c.*, t.nombre AS tipo_nombre, m.nombre AS material_nombre
             FROM cotizaciones c
             LEFT JOIN tipos_mueble t ON t.id = c.tipo_mueble_id
             LEFT JOIN materiales m ON m.id = c.material_id
             WHERE c.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $img = $this->db->prepare("SELECT * FROM cotizacion_imagenes WHERE cotizacion_id = :id");
        $img->execute([':id' => $id]);
        $row['imagenes'] = $img->fetchAll();

        $resp = $this->db->prepare(
            "SELECT r.*, u.nombre AS admin_nombre
             FROM cotizacion_respuestas r
             LEFT JOIN usuarios u ON u.id = r.admin_id
             WHERE r.cotizacion_id = :id ORDER BY r.created_at ASC"
        );
        $resp->execute([':id' => $id]);
        $row['respuestas'] = $resp->fetchAll();
        return $row;
    }

    public function responder(int $id, int $adminId, string $mensaje): void {
        $this->db->prepare(
            "INSERT INTO cotizacion_respuestas (cotizacion_id, admin_id, mensaje) VALUES (:id, :admin, :msg)"
        )->execute([':id' => $id, ':admin' => $adminId, ':msg' => $mensaje]);

        $this->db->prepare("UPDATE cotizaciones SET estado = 'respondida' WHERE id = :id")
            ->execute([':id' => $id]);
    }

    public function materiales(): array {
        return $this->db->query("SELECT * FROM materiales WHERE activo = 1 ORDER BY nombre")->fetchAll();
    }

    public function tipos(): array {
        return $this->db->query("SELECT * FROM tipos_mueble WHERE activo = 1 ORDER BY nombre")->fetchAll();
    }

    public function contarPorEstado(): array {
        $conteo = ['pendiente' => 0, 'en_revision' => 0, 'respondida' => 0, 'cerrada' => 0];
        foreach ($this->db->query("SELECT estado, COUNT(*) AS total FROM cotizaciones GROUP BY estado")->fetchAll() as $row) {
            $conteo[$row['estado']] = (int) $row['total'];
        }
        return $conteo;
    }

    public function recientes(int $limite = 8): array {
        $limite = max(1, min(20, $limite));
        $sql = "SELECT c.id, c.nombre_contacto, c.email, c.telefono, c.presupuesto, c.estado, c.created_at,
                       t.nombre AS tipo_nombre
                FROM cotizaciones c
                LEFT JOIN tipos_mueble t ON t.id = c.tipo_mueble_id
                ORDER BY c.created_at DESC, c.id DESC
                LIMIT {$limite}";
        return $this->db->query($sql)->fetchAll();
    }
}