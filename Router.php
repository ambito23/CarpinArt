<?php
// Router.php

class Router {
    // Se remueve 'array' antes de $routes para compatibilidad
    private $routes = [];

    // Registrar rutas HTTP GET
    public function get(string $path, array $handler): void {
        $this->routes['GET'][$this->formatPath($path)] = $handler;
    }

    // Registrar rutas HTTP POST
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$this->formatPath($path)] = $handler;
    }

    private function formatPath(string $path): string {
        $path = trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function dispatch(string $uri, string $method): void {
        $uri = explode('?', $uri)[0];
        $path = $this->formatPath($uri);
        $method = strtoupper($method);

        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $action] = $this->routes[$method][$path];

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>La ruta '{$path}' no existe en CarpinArt.</p>";
    }
}