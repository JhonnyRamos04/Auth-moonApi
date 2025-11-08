<?php
// Se establece la cabecera de la respuesta como json
header("Content-Type: application/json;charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar petición pre-flight de CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir archivos necesarios
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Core/Router.php';

// **CORRECCIÓN: Usar PATH_INFO en lugar de $_GET['url']**
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Obtener la URL desde PATH_INFO (funciona mejor con el servidor built-in de PHP)
if (isset($_SERVER['PATH_INFO'])) {
    $url = $_SERVER['PATH_INFO'];
} else {
    // Fallback: usar REQUEST_URI y eliminar query strings
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Eliminar el base path si es necesario
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath != '/' && strpos($url, $basePath) === 0) {
        $url = substr($url, strlen($basePath));
    }
}

$urlParts = explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));

// DEPURACIÓN: Mostrar información de la URL
echo "<!-- DEBUG INFO:\n";
echo "REQUEST_METHOD: " . $requestMethod . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "\n";
echo "URL: " . $url . "\n";
echo "URL Parts: "; print_r($urlParts);
echo "-->";

// Instanciar el router
$router = new Router();

// --- Definir las rutas de la API de autenticación ---
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');

// Ejecutar el enrutador
$router->route($urlParts, $requestMethod);