<?php
// ============================================================
// core/Router.php — Front Controller Router
// ============================================================

class Router {
    private array $routes = [];

    // Register GET route
    public function get(string $path, string $action): void {
        $this->routes['GET'][$path] = $action;
    }

    // Register POST route
    public function post(string $path, string $action): void {
        $this->routes['POST'][$path] = $action;
    }

    // Dispatch request to appropriate controller action
    public function dispatch(string $method, string $uri): void {
        // Remove query string
        $uri = strtok($uri, '?');
        // Normalize trailing slash
        $uri = rtrim($uri, '/') ?: '/';

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => $action) {
            $regex = $this->patternToRegex($pattern);

            if (preg_match($regex, $uri, $matches)) {
                // Extract named params ({id}, etc.)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerName, $methodName] = explode('@', $action);

                // Autoload if needed
                $controllerFile = BASE_PATH . '/controllers/' . $controllerName . '.php';
                if (!class_exists($controllerName) && file_exists($controllerFile)) {
                    require_once $controllerFile;
                }

                if (!class_exists($controllerName)) {
                    $this->abort(500, "Controller [{$controllerName}] not found.");
                    return;
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $methodName)) {
                    $this->abort(500, "Method [{$methodName}] not found in [{$controllerName}].");
                    return;
                }

                // Pass matched URL params as arguments
                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }

        // No route matched
        $this->abort(404, 'Page Not Found');
    }

    // Convert route pattern like /employees/{id}/edit to a named regex
    private function patternToRegex(string $pattern): string {
        $escaped = preg_quote($pattern, '#');
        // Replace \{param\} with named capture group (?P<param>[^/]+)
        $regex = preg_replace('/\\\{(\w+)\\\}/', '(?P<$1>[^/]+)', $escaped);
        return '#^' . $regex . '$#';
    }

    private function abort(int $code, string $message): void {
        http_response_code($code);
        echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
    }
}
