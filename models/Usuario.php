<?php

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function buscarPorEmail(string $email): ?array {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id
                WHERE u.email = :email
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function crear(array $datos): int {
        $sql = "INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono, activo)
                VALUES (:rol_id, :nombre, :apellido, :email, :password_hash, :telefono, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':rol_id'        => $datos['rol_id'],
            ':nombre'        => $datos['nombre'],
            ':apellido'      => $datos['apellido'],
            ':email'         => $datos['email'],
            ':password_hash' => $datos['password_hash'],
            ':telefono'      => $datos['telefono'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function idRolCliente(): int {
        $stmt = $this->db->prepare("SELECT id FROM roles WHERE nombre = 'cliente' LIMIT 1");
        $stmt->execute();
        return (int) ($stmt->fetchColumn() ?: 1);
    }
}
