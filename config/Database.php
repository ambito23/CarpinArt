<?php
// config/Database.php

class Database {
    // Única instancia de la clase (Patrón Singleton)
    private static ?Database $instance = null;
    
    // Objeto PDO para la conexión activa
    private ?PDO $connection = null;

    /**
     * El constructor se define privado para evitar que la clase 
     * sea instanciada directamente con 'new Database()' fuera de aquí.
     */
    private function __construct() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                // Forzar manejo de errores mediante excepciones
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Formato de retorno por defecto: Arreglo asociativo
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Desactivar emulación de prepared statements para mayor seguridad contra SQL Injection
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Usar conexiones persistentes opcionalmente (falso por defecto para evitar bloqueos)
                PDO::ATTR_PERSISTENT         => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            // En entorno de desarrollo mostramos el error detallado
            // En producción, es recomendable registrar en logs y mostrar un mensaje genérico
            error_log("Error de conexión a la Base de Datos: " . $e->getMessage());
            die("Error crítico: No se pudo conectar a la base de datos de CarpinArt.");
        }
    }

    /**
     * Evitar la clonación de la instancia (Principio Singleton)
     */
    private function __clone() {}

    /**
     * Evitar la deserialización de la instancia
     */
    public function __wakeup() {
        throw new \Exception("No se puede deserializar una instancia de la clase Singleton Database.");
    }

    /**
     * Método estático global para obtener la única instancia de conexión.
     * 
     * @return PDO
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance->connection;
    }

    /**
     * Alias de getInstance() para modelos que aún llaman Database::connect().
     */
    public static function connect(): PDO {
        return self::getInstance();
    }
}