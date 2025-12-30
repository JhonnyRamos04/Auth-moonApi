<?php
// Core/Database.php
class Database
{
    private static $pdo = null;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                // En producción no devuelvas $e->getMessage() completo por seguridad
                echo json_encode(['error' => 'Error de conexión a la base de datos']);
                exit;
            }
        }
        return self::$pdo;
    }
}