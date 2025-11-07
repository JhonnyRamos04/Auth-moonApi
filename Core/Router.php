<?php

class Router
{
    private $routes = [];

    // Metodo para definir una ruta GET
    public function get($path, $controllerAction)
    {
        $this->addRoute('GET', $path, $controllerAction);
    }

    // Metodo para definir una ruta POST
    public function post($path, $controllerAction)
    {
        $this->addRoute('POST', $path, $controllerAction);
    }

    // Método privado para añadir rutas
    private function addRoute($method, $path, $controllerAction)
    {
        $this->routes[$method][$path] = $controllerAction;
    }


    // Metodo para enrutar la peticion
    public function route($urlParts, $requestMethod)
    {
        // Construir la ruta a partir de los partes de la URL
        $path = '/' . implode('/', $urlParts);

        // Si la URL está vacía, la ruta es '/'
        if (empty($urlParts[0])) {
            $path = '/';
        }

        $routesForMethod = $this->routes[$requestMethod] ?? [];

        // 1) Coincidencia exacta
        if (isset($routesForMethod[$path])) {
            $this->dispatch($routesForMethod[$path]);
            return;
        }

        // 2) Coincidencia con parámetros (ej: /products/123)
        foreach ($routesForMethod as $definedPath => $controllerAction) {
            // Busca rutas que terminen en un patrón como '/{id}'
            // Esta es una simplificación y podría mejorarse con expresiones regulares
            if (strpos($definedPath, '{') !== false && strpos($path, rtrim(explode('{', $definedPath)[0], '/')) === 0) {
                $pathParts = explode('/', trim($path, '/'));
                $definedPathParts = explode('/', trim($definedPath, '/'));

                if (count($pathParts) === count($definedPathParts)) {
                    $params = [];
                    // Extraer el valor del parámetro
                    $idPart = end($pathParts);
                    $this->dispatch($controllerAction, [$idPart]);
                    return;
                }
            }
        }

        // No se encontró ruta
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['message' => 'Ruta no encontrada']);
    }

    private function dispatch($controllerAction, $params = [])
    {
        list($controllerName, $action) = explode('@', $controllerAction);
        $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['message' => 'Controlador no encontrado: ' . $controllerName]);
            return;
        }

        require_once $controllerFile;
        $controller = new $controllerName();
        // Llama al método del controlador, pasando los parámetros
        call_user_func_array([$controller, $action], $params);
    }
}
