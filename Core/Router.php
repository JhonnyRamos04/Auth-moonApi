<?php

class Router
{
    // Array para almacenar las rutas
    private $routes = [];

    // Metodo para definir una ruta GET
    public function get($path, $controllerAction)
    {
        $this->routes['GET'][$path] = $controllerAction;
    }

    // Metodo para enrutar la peticion
    public function route($urlParts, $requestMethod)
    {
        // Construir la ruta a partir de los partes de la URL
        $path = '/' . implode('/', $urlParts);

        $routesForMethod = $this->routes[$requestMethod] ?? [];

        // 1) Coincidencia exacta
        if (isset($routesForMethod[$path])) {
            $controllerAction = $routesForMethod[$path];
            list($controllerName, $action) = explode('@', $controllerAction);
            $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                header("HTTP/1.0 500 Internal Server Error");
                echo json_encode(['message' => 'Controlador no encontrado']);
                return;
            }
            require_once $controllerFile;
            $controller = new $controllerName();
            $controller->$action();
            return;
        }

        // 2) Intentar coincidencia por prefijo para rutas que aceptan un id
        foreach ($routesForMethod as $definedPath => $controllerAction) {
            // si la ruta definida es un prefijo del path solicitado (ej: /products matches /products/2)
            if ($definedPath !== '/' && strpos($path, $definedPath . '/') === 0) {
                $idPart = substr($path, strlen($definedPath) + 1);
                list($controllerName, $action) = explode('@', $controllerAction);
                $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';
                if (!file_exists($controllerFile)) {
                    header("HTTP/1.0 500 Internal Server Error");
                    echo json_encode(['message' => 'Controlador no encontrado']);
                    return;
                }
                require_once $controllerFile;
                $controller = new $controllerName();
                if ($idPart !== '') {
                    // pasar ID como argumento
                    $controller->$action($idPart);
                } else {
                    $controller->$action();
                }
                return;
            }
        }

        // No se encontró ruta
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['message' => 'Ruta no encontrada']);
    }
    // private $url;

    // public function __construct($url)
    // {
    //     $this->url = $url;
    // }

    // public function getUrlParts()
    // {
    //     return explode('/', filter_var(trim($this->url, '/'), FILTER_SANITIZE_URL));
    // }
}
