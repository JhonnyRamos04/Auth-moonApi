<?php
// Se establece la cabecera de la respuesta como json
header("Content-Type: application/json;charset= UTF-8");
header("Access-Control-Allow-Origin:*"); // se acceptan todo tipo de origenes para evitar el cors
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // se aceptan todos los metodos http
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400"); // cache por 1 dia

// incluir el router
require_once __DIR__ . '/Core/Router.php';

// Obtener los detalles de la peticion 

$requestMethod = $_SERVER['REQUEST_METHOD'];
$url = isset($_GET['url']) ? $_GET['url'] : '';
$urlParts = explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));

// Instanciar el router
$router = new Router();

// Definir las rutas y controladores
$router->get('/products', 'ProductController@index');

// Ejectur el enrutador
$router->route($urlParts, $requestMethod);


// //Se captura la Url 
// $url = isset($_GET['url']) ? $_GET['url'] : '';

// // Limpiar y dividir la url
// $url = explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));

// // Se crea un objeto de tipo response 
// $response = [
//     "status" => "success",
//     "requested_url" => $_GET['url'] ?? '',
//     "url_parts" => $url,
//     "method" => $_SERVER['REQUEST_METHOD'] // Se obtiene el metodo solo para info
// ];

// echo json_encode($response); // retorna un json como respuesta
