<?php

namespace Router;

use Controllers\Controller;

class Router {
    private static array $routes = array();
    private static array $routeAliases = array();

    private function __construct() { } // Disable instantiation
    public function __clone() {} // Disable cloning
    public function __wakeup() {} // Disable de-serialization

    public static function __renameRoute(Route $route, array $prevAliases, array $newAliases): void {
        foreach ($prevAliases as $alias) unset(self::$routeAliases[$alias]);
        foreach ($newAliases as $alias) self::$routeAliases[$alias] = $route->name;
    }

    /**
     * Define a GET route.
     * @param string $path
     * @param callable|array $action
     * @param null|string $name
     * @return Route
     */
    public static function get(string $path, callable|array $action, null|string $name = null): Route {
        $route = new Route('GET', $path, $action, $name, '__renameRoute');
        if (is_array($route->name)) {
            foreach ($route->name as $name)
                self::$routes[$name] = $route;
        } else {
            self::$routes[$route->name] = $route;
        }
        return $route;
    }

    /**
     * Define a POST route.
     * @param string $uri
     * @param callable|array $action
     * @return Route
     */
    public function post(string $path, callable|array $action, null|string $name = null): Route {
        $route = new Route('POST', $path, $action, $name, '__renameRoute');
        if (is_array($route->name)) {
            foreach ($route->name as $name)
                self::$routes[$name] = $route;
        } else {
            self::$routes[$route->name] = $route;
        }
        return $route;
    }

    /**
     * Dispatch the current request to the appropriate action.
     */
    public static function dispatch(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        $routeName = Route::__getUniqueName($method, $uri);
        /** @var Route|null $route */
        $route = self::$routes[$routeName] ?? null;
        /** @var array|false $arguments */
        $arguments = false;

        /**
         * Find corresponding route.
         * @var string $routeName
         * @var Route $potentialRoute
         */
        foreach (self::$routes as $routeName => $potentialRoute) {
            if (
                (is_array($potentialRoute->method) && in_array($method, $potentialRoute->method))
                || $method !== $potentialRoute->method
            ) continue;
            $matches = $potentialRoute->match($uri);
            if ($matches !== false) {
                $route = $potentialRoute;
                $arguments = $matches;
                break;
            }
        }

        if (!$route) {
            http_response_code(404);
            include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '404.html';
            return;
        }

        if (
            (is_array($route->method) && in_array($method, $route->method))
            || $method !== $route->method
        ) {
            http_response_code(405);
            header('Allow: ' . (is_array($route->method) ? implode(', ', $route->method) : $route->method));
            include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '405.html';
            return;
        }

        $action = $route->action;

        if (!$action) {
            http_response_code(500);
            include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . 'unknown.html';
            return;
        }

        if (config('ENVIRONMENT') === 'production') {
            try {
                if (is_callable($action)) {
                    resolveAndCallFunction($action, $arguments);
                } elseif (is_array($action)) {
                    [$controller, $method] = $action;
                    resolveAndCallMethod($method, new $controller(), $arguments);
                }
            } catch (\Error | \Exception $e) {
                http_response_code(500);
                include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . 'unknown.html';
                return;
            }
        } else {
            if (is_callable($action)) {
                resolveAndCallFunction($action, $arguments);
            } elseif (is_array($action)) {
                [$controller, $method] = $action;
                resolveAndCallMethod($method, new $controller(), $arguments);
            }
        }
    }

    /**
     * Render a view template inside a layout.
     * @see Controller::render() - Prefer controller architecture.
     * @param string $view Path to the view file (e.g., 'pages/index').
     * @param array $data Data to pass to the view.
     * @param string|null $layout Layout file (default: 'layouts/main') or null if no layout is needed.
     */
    public static function render(string $view, array $data = [], string|null $layout = 'layouts/main'): void {
        extract($data);

        if ($layout === null) {
            require VIEW_DIR . "$view.php";
            return;
        }

        ob_start();
        require VIEW_DIR . "$view.php";
        $content = ob_get_clean();

        require VIEW_DIR . "$layout.php";
    }

    /**
     * @param string $name
     * @return string The route's URL.
     */
    public static function route(string $name): string {
        /** @var Route $route */
        $route = self::$routes[self::$routeAliases[$name]] ?? self::$routes[$name];
        return $route?->path ?? $name;
    }
}