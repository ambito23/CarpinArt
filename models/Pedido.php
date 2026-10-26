
<?php

class Pedido {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->asegurarTabla();
    }

    private function asegurarTabla(): void {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS pedidos (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                producto_id INT UNSIGNED NOT NULL,
                producto_nombre VARCHAR(120) NOT NULL,
                cantidad SMALLINT UNSIGNED NOT NULL DEFAULT 1,
                precio_unitario DECIMAL(12,2) NOT NULL DEFAULT 0,
                total DECIMAL(12,2) NOT NULL DEFAULT 0,
                usuario_id INT UNSIGNED NULL,
                nombre_contacto VARCHAR(80) NOT NULL,
                telefono VARCHAR(20) NOT NULL,
                email VARCHAR(120) NOT NULL,
                ciudad VARCHAR(80) NULL,
                direccion VARCHAR(255) NOT NULL,
                notas TEXT NULL,
                quiere_whatsapp TINYINT(1) NOT NULL DEFAULT 0,
                stock_aplicado TINYINT(1) NOT NULL DEFAULT 1,
                estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=MyISAM"
        );
    }

    public function crear(array $datos): int {
        $sql = "INSERT INTO pedidos (
                    producto_id, producto_nombre, cantidad, precio_unitario, total, usuario_id,
                    nombre_contacto, telefono, email, ciudad, direccion, notas, quiere_whatsapp, stock_aplicado, estado
                ) VALUES (
                    :producto_id, :producto_nombre, :cantidad, :precio_unitario, :total, :usuario_id,
                    :nombre_contacto, :telefono, :email, :ciudad, :direccion, :notas, :quiere_whatsapp, 1, 'pendiente'
                )";
        $this->db->prepare($sql)->execute($datos);
        return (int) $this->db->lastInsertId();
    }

    public function todos(): array {
        return $this->db->query("SELECT * FROM pedidos ORDER BY created_at DESC, id DESC")->fetchAll();
    }

    public function buscar(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function pendientes(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'")->fetchColumn();
    }

    public function cambiarEstado(int $id, string $estado): void {
        $permitidos = ['pendiente', 'confirmado', 'en_proceso', 'entregado', 'cancelado'];
        if (!in_array($estado, $permitidos, true)) {
            return;
        }

        $pedido = $this->buscar($id);
        if (!$pedido) {
            return;
        }

        $anterior = $pedido['estado'];
        if ($anterior === $estado) {
            return;
        }

        if ($estado === 'cancelado' && (int) $pedido['stock_aplicado'] === 1) {
            (new Producto())->ajustarStock((int) $pedido['producto_id'], (int) $pedido['cantidad']);
            $this->db->prepare("UPDATE pedidos SET estado = :e, stock_aplicado = 0 WHERE id = :id")
                ->execute([':e' => $estado, ':id' => $id]);
            return;
        }

        if ($anterior === 'cancelado' && $estado !== 'cancelado' && (int) $pedido['stock_aplicado'] === 0) {
            (new Producto())->ajustarStock((int) $pedido['producto_id'], -1 * (int) $pedido['cantidad']);
            $this->db->prepare("UPDATE pedidos SET estado = :e, stock_aplicado = 1 WHERE id = :id")
                ->execute([':e' => $estado, ':id' => $id]);
            return;
        }

        $this->db->prepare("UPDATE pedidos SET estado = :e WHERE id = :id")
            ->execute([':e' => $estado, ':id' => $id]);
    }

    public function enEspera(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM pedidos WHERE estado IN ('pendiente','confirmado','en_proceso')"
        )->fetchColumn();
    }

    public function completados(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM pedidos WHERE estado = 'entregado'"
        )->fetchColumn();
    }

    public function contarPorEstado(): array {
        $conteo = ['pendiente' => 0, 'confirmado' => 0, 'en_proceso' => 0, 'entregado' => 0, 'cancelado' => 0];
        foreach ($this->db->query("SELECT estado, COUNT(*) AS total FROM pedidos GROUP BY estado")->fetchAll() as $row) {
            $conteo[$row['estado']] = (int) $row['total'];
        }
        return $conteo;
    }

    public function topVendidos(int $limite = 5): array {
        $stmt = $this->db->prepare(
            "SELECT producto_id, producto_nombre,
                    SUM(cantidad) AS unidades,
                    SUM(total) AS ingresos,
                    COUNT(*) AS num_pedidos
             FROM pedidos
             WHERE estado != 'cancelado'
             GROUP BY producto_id, producto_nombre
             ORDER BY unidades DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function recientes(int $limite = 10): array {
        $stmt = $this->db->prepare("SELECT * FROM pedidos ORDER BY created_at DESC, id DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function totalVentas(): float {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(total),0) FROM pedidos WHERE estado != 'cancelado'"
        )->fetchColumn();
    }

    public function total(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM pedidos')->fetchColumn();
    }

    public function ticketPromedio(): float {
        return (float) $this->db->query(
            "SELECT COALESCE(AVG(total),0) FROM pedidos WHERE estado != 'cancelado'"
        )->fetchColumn();
    }

    public function ventasDelMes(): float {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(total),0) FROM pedidos
             WHERE estado != 'cancelado'
               AND YEAR(created_at) = YEAR(CURDATE())
               AND MONTH(created_at) = MONTH(CURDATE())"
        )->fetchColumn();
    }

    public function ventasMensuales(int $meses = 6): array {
        $meses = max(3, min(12, $meses));
        $filas = $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes,
                    COUNT(*) AS pedidos,
                    COALESCE(SUM(CASE WHEN estado != 'cancelado' THEN total ELSE 0 END), 0) AS ingresos
             FROM pedidos
             WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL " . ($meses - 1) . " MONTH), '%Y-%m-01')
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')"
        )->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

        $out = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $clave = date('Y-m', strtotime('-' . $i . ' months'));
            $fila = $filas[$clave] ?? null;
            $out[] = [
                'mes'      => $clave,
                'pedidos'  => $fila ? (int) $fila['pedidos'] : 0,
                'ingresos' => $fila ? (float) $fila['ingresos'] : 0,
            ];
        }
        return $out;
    }
}