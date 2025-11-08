<?php
class Database
{
    private static $pdo = null;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            try {
                // Construir el data source name (DSN)
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                echo "<!-- Intentando conectar a: $dsn -->"; // DEBUG
                
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                
                echo "<!-- Conexión exitosa -->"; // DEBUG
                
            } catch (PDOException $e) {
                // Info del error para depuración
                $error_info = [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'host' => DB_HOST,
                    'dbname' => DB_NAME,
                    'user' => DB_USER
                ];
                
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode([
                    'error' => 'Error de configuración de base de datos',
                    'debug' => $error_info
                ]);
                exit;
            }
        }
        return self::$pdo;
    }
}