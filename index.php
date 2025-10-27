<?php
// Se establece la cabecera de la respuesta como json
header("Content-Type: application/json;charset= UTF-8");
header("Access-Control-Allow-Origin:*"); // se acceptan todo tipo de origenes para evitar el cors

//Se captura la Url 
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Limpiar y dividir la url
$url = explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));

// Se crea un objeto de tipo response 
$response = [
"status" => "success",
"requested_rul" => $_GET['url'],
"url_parts" => $url,
"method"=> $_SERVER['REQUEST_METHOD'] // Se obtiene el metodo solo para info
];

echo json_encode($response);// retorna un json como respuesta
?>