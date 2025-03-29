<?php

class Router
{
    private static $routes = [];
    private static $middlewares = [];
    private static $params = [];

    public function add(
        string $path,
        $method,
        callable $action,
        array $middlewares = []
    ): void {
        // check if the route has param
        if (str_contains($path, ":")) {
            $pathParts = explode(":", $path);
            $newPath = substr($pathParts[0], 0, -1);

            $param = $pathParts[1];
            self::$routes[$newPath][$method] = $action;
            self::$middlewares[$newPath][$method] = $middlewares;
            self::$params[$newPath][$method] = $param;
            return;
        }

        // if the route has no param
        self::$routes[$path][$method] = $action;
        self::$middlewares[$path][$method] = $middlewares;

    }

    // function that call the controller based on the path and the method
    public function dispatch(string $path, string $method)
    {
        $pathParts = explode("/", $path);
        $prefix  = "/";

        foreach ($pathParts as $pathElement) {
            if ($pathElement != "") {
                $prefix .= $pathElement;
                if (array_key_exists($prefix, self::$params)) {
                    if (array_key_exists($method, self::$params[$prefix])) {
                        $param = join("", array_diff($pathParts, explode("/", $prefix)));
                        // execute the middlwares if it has it
                        self::executeMiddlwares($prefix, $method);
                        self::callTheHandler($prefix, $method, $param);
                        return;
                    }
                }
                $prefix .= "/";
            }
        }

        if (array_key_exists($path, self::$routes)) {
            if (array_key_exists($method, self::$routes[$path])) {
                // run the middwares first
                $handler = self::$routes[$path][$method];
                self::executeMiddlwares($path, $method);
                call_user_func($handler);
                return;
            }
        }
        require __DIR__  . "/../views/pages/notFound.html";
    }

    private static function executeMiddlwares($path, $method)
    {

        if (array_key_exists($path, self::$middlewares)) {
            if (array_key_exists($method, self::$middlewares[$path])) {
                foreach (self::$middlewares[$path][$method] as $middleware) {
                    call_user_func($middleware);
                }
            }
        }
    }

    private static function callTheHandler($path, $method, $param = null)
    {
        if (array_key_exists($path, self::$routes)) {
            if (array_key_exists($method, self::$routes[$path])) {
                self::executeMiddlwares($path, $method);
                // run the middwares first
                $handler = self::$routes[$path][$method];
                if ($param != null) {
                    call_user_func($handler, $param);
                } else {
                    call_user_func($handler);
                }
            }
        }
    }

}
