<?php

class MensajeContacto {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function crear(string $nombre, string $celular, string $mensaje): int {
        $stmt = $this->db->prepare(
            "INSERT INTO mensajes_contacto (nombre, celular, mensaje) VALUES (:n, :c, :m)"
        );
        $stmt->execute([':n' => $nombre, ':c' => $celular, ':m' => $mensaje]);
        return (int) $this->db->lastInsertId();
    }

    public function todos(): array {
        return $this->db->query("SELECT * FROM mensajes_contacto ORDER BY created_at DESC")->fetchAll();
    }

    public function marcarLeido(int $id): void {
        $this->db->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id = :id")->execute([':id' => $id]);
    }

    public function noLeidos(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn();
    }
}
