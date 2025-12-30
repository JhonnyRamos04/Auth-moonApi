<?php
// index.php

// 1. Configuración de cabeceras
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Manejo de pre-flight (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Incluir archivos
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Core/Router.php';

// 4. Lógica de Enrutamiento
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Obtener la ruta limpia. 
// Parseamos la URI para separar el path de los query params (?id=1)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlParts = explode('/', filter_var(trim($uri, '/'), FILTER_SANITIZE_URL));

// Instanciar el router
$router = new Router();

// --- Rutas ---
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');

// Ejecutar
$router->route($urlParts, $requestMethod);