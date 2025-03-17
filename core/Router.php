<?php

class Router
{
    private $routes = [];

    /**
     * Register a GET route.
     *
     * @param string $path The URL path.
     * @param callable $callback The controller and method to invoke.
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Register a POST route.
     *
     * @param string $path The URL path.
     * @param callable $callback The controller and method to invoke.
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Dispatch the current request to the matching route.
     *
     * @param string $currentPath The requested URL.
     * @return void
     */
    public function dispatch($currentPath)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Normalize the path (remove query parameters)
        $currentPath = strtok($currentPath, '?');

        if (isset($this->routes[$method][$currentPath])) {
            $callback = $this->routes[$method][$currentPath];

            // Call the controller's method
            if (is_callable($callback)) {
                call_user_func($callback);
            } elseif (is_array($callback)) {
                $controller = $callback[0];
                $method = $callback[1];

                if (method_exists($controller, $method)) {
                    call_user_func([$controller, $method]);
                } else {
                    http_response_code(404);
                    echo "Method not found.";
                }
            }
        } else {
            http_response_code(404);
            echo "Route not found.";
            echo    $currentPath;

        }
    }
}
