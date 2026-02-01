<?php
/**
 * Script de inicialización de base de datos para Auth-moonApi
 * Ejecuta el script SQL para crear todas las tablas necesarias
 */

require_once __DIR__ . '/../config.php';

try {
    echo "🔌 Conectando a MySQL...\n";

    // Conectar sin especificar base de datos
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    echo "✅ Conexión establecida\n";
    echo "📦 Creando base de datos y tablas...\n";

    // Leer el script SQL
    $sqlPath = __DIR__ . '/../../database/init.sql';
    if (!file_exists($sqlPath)) {
        throw new Exception("Archivo init.sql no encontrado en: $sqlPath");
    }

    $sqlScript = file_get_contents($sqlPath);

    // Ejecutar el script
    $pdo->exec($sqlScript);

    echo "✅ Base de datos inicializada correctamente\n";
    echo "📋 Tablas creadas: users, clients, invoice, invoice_details\n";

} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
