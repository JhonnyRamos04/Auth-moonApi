<?php

class Router
{
    private $routes = [];

    public function get($path, $controllerAction)
    {
        $this->addRoute('GET', $path, $controllerAction);
    }

    public function post($path, $controllerAction)
    {
        $this->addRoute('POST', $path, $controllerAction);
    }

    private function addRoute($method, $path, $controllerAction)
    {
        $this->routes[$method][$path] = $controllerAction;
    }

    public function route($urlParts, $requestMethod)
    {
        // Construir la ruta a partir de los partes de la URL
        $path = '/' . implode('/', $urlParts);

        // Si la URL está vacía, la ruta es '/'
        if (empty($urlParts[0])) {
            $path = '/';
        }

        // DEPURACIÓN: Mostrar información de routing
        echo "<!-- DEBUG Router:\n";
        echo "Path: " . $path . "\n";
        echo "Method: " . $requestMethod . "\n";
        echo "Available routes: "; print_r($this->routes[$requestMethod] ?? []);
        echo "-->";

        $routesForMethod = $this->routes[$requestMethod] ?? [];

        // 1) Coincidencia exacta
        if (isset($routesForMethod[$path])) {
            $this->dispatch($routesForMethod[$path]);
            return;
        }

        // 2) Coincidencia con parámetros (simplificada)
        foreach ($routesForMethod as $definedPath => $controllerAction) {
            if (strpos($definedPath, '{') !== false) {
                $basePath = rtrim(explode('{', $definedPath)[0], '/');
                if (strpos($path, $basePath) === 0) {
                    $pathParts = explode('/', trim($path, '/'));
                    $definedPathParts = explode('/', trim($definedPath, '/'));
                    
                    if (count($pathParts) === count($definedPathParts)) {
                        $param = end($pathParts);
                        $this->dispatch($controllerAction, [$param]);
                        return;
                    }
                }
            }
        }

        // No se encontró ruta
        header("HTTP/1.0 404 Not Found");
        echo json_encode([
            'message' => 'Ruta no encontrada',
            'debug' => [
                'path' => $path,
                'method' => $requestMethod,
                'available_routes' => array_keys($routesForMethod)
            ]
        ]);
    }

    private function dispatch($controllerAction, $params = [])
    {
        list($controllerName, $action) = explode('@', $controllerAction);
        
        // DEPURACIÓN: Mostrar información del controlador
        echo "<!-- DEBUG Dispatch:\n";
        echo "Controller: " . $controllerName . "\n";
        echo "Action: " . $action . "\n";
        echo "Params: "; print_r($params);
        echo "-->";
        
        $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['message' => 'Controlador no encontrado: ' . $controllerName]);
            return;
        }

        require_once $controllerFile;
        
        // Verificar que la clase existe
        if (!class_exists($controllerName)) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['message' => 'Clase no encontrada: ' . $controllerName]);
            return;
        }
        
        $controller = new $controllerName();
        
        // Verificar que el método existe
        if (!method_exists($controller, $action)) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['message' => 'Método no encontrado: ' . $action]);
            return;
        }
        
        call_user_func_array([$controller, $action], $params);
    }
}