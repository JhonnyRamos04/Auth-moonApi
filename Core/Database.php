<?php

class Database
{
    private static $pdo = null;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            try {
                // Construir el DSN (Data Source Name)
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

                // Opciones de PDO
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna arrays asociativos
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa preparaciones nativas de la BD
                ];

                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // En un entorno real, loguearías este error, no lo mostrarías al usuario
                // header('HTTP/1.1 500 Internal Server Error');
                // echo json_encode(['message' => 'Error de conexión a la base de datos.']);
                die('Error de conexión: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
