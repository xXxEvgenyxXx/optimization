<?php 

namespace App\Core;
use App\Controllers\MaterialController;
use App\Controllers\ProductController;

class Router{

    public array $routes = [];

    public function get(string $path, callable|array $handler ):void {
        $this -> routes['GET'][$path][] = $handler;
    }

    public function post(string $path, callable|array $handler ):void {
        $this -> routes['POST'][$path][] = $handler;
    }

    public function dispatch(string $method, string $uri): void { //Переписал dispatch чтоб поддерживал сразу несколько выводов
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route => $handlers) {
            $pattern = '#^' . preg_replace('#\{(\w+)}#', '(\d+)', $route) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                foreach ($handlers as $handler) {
                    if (is_array($handler)) {
                        [$class, $action] = $handler;
                        (new $class())->$action($matches);
                    } else {
                        $handler($matches);
                    }
                }
                return;
            }
        }

        http_response_code(404);
        echo "100 отжиманий";
    }
}
