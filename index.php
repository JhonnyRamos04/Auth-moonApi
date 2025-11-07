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

// Obtener los detalles de la peticion
$requestMethod = $_SERVER['REQUEST_METHOD'];
$url = isset($_GET['url']) ? $_GET['url'] : '';
$urlParts = explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));

// Instanciar el router
$router = new Router();

// --- Definir las rutas de la API de autenticación ---
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');

// ... aquí puedes añadir más rutas, por ejemplo:
// $router->get('/products', 'ProductController@index');
// $router->get('/products/{id}', 'ProductController@show');


// Ejecutar el enrutador
$router->route($urlParts, $requestMethod);
