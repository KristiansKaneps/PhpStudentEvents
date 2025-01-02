<?php

namespace Router;

use Controllers\Controller;
use Localization\Localization;

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
     * Define a route.
     * @param string $path
     * @param callable|array $action
     * @param null|string $name
     * @return Route
     */
    public static function define(string|array $method, string $path, callable|array $action, null|string $name = null): Route {
        $route = new Route($method, $path, $action, $name, '__renameRoute');
        if (is_array($route->name)) {
            foreach ($route->name as $name)
                self::$routes[$name] = $route;
        } else {
            self::$routes[$route->name] = $route;
        }
        return $route;
    }

    /**
     * Define a GET route.
     * @param string $path
     * @param callable|array $action
     * @param null|string $name
     * @return Route
     */
    public static function get(string $path, callable|array $action, null|string $name = null): Route {
        return self::define('GET', $path, $action, $name);
    }

    /**
     * Define a POST route.
     * @param string $path
     * @param callable|array $action
     * @param null|string $name
     * @return Route
     */
    public static function post(string $path, callable|array $action, null|string $name = null): Route {
        return self::define('POST', $path, $action, $name);
    }

    /**
     * Get existing or generate a new CSRF token.
     * @throws \Exception If an appropriate source of randomness cannot be found.
     */
    public static function getCsrfToken(): string {
        if (empty($_SESSION['csrf-token'])) {
            $_SESSION['csrf-token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf-token'];
    }

    /**
     * Dispatch the current request to the appropriate action.
     */
    public static function dispatch(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        // Resolve appropriate locale.
        $acceptedLocale = null;
        $locales = Localization::getLocales();
        foreach ($locales as $locale) {
            if (str_starts_with($uri, '/'.$locale.'/')) {
                $acceptedLocale = $locale;
                $uri = substr($uri, 3);
                break;
            } else if (strlen($uri) === 3 && str_starts_with($uri, '/'.$locale)) {
                $acceptedLocale = $locale;
                $uri = '/'.substr($uri, 3);
                break;
            }
        }
        if ($acceptedLocale === null && isset($_SESSION['locale'])){
            if (in_array($_SESSION['locale'], $locales, true))
                $acceptedLocale = $_SESSION['locale'];
        }
        if ($acceptedLocale === null) {
            $languages = array();
            $languageItems = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            foreach ($languageItems as $item) {
                $parts = explode(';q=', $item);
                $language = str_replace('-', '_', trim($parts[0]));
                $qValue = isset($parts[1]) ? (float) $parts[1] : 1.0;
                $languages[$language] = $qValue;
            }
            arsort($languages, SORT_NUMERIC);

            foreach (array_keys($languages) as $lang) {
                $lang = strtolower($lang);
                $langUnderscorePos = strpos($lang, '_');
                foreach ($locales as $locale) {
                    $locale = strtolower($locale);
                    if ($locale === $lang) {
                        $acceptedLocale = $locale;
                        break 2;
                    } else if ($locale === substr($lang, 0, $langUnderscorePos)) {
                        $acceptedLocale = $locale;
                        break 2;
                    }
                }
            }
        }
        Localization::setCurrentLocale($acceptedLocale);

        // Resolve route.
        $routeName = Route::__getUniqueName($method, $uri);
        /** @var Route|null $route */
        $route = self::$routes[$routeName] ?? null;
        /** @var array|null $arguments */
        $arguments = null;

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
            exit;
        }

        if (
            (is_array($route->method) && !in_array($method, $route->method))
            || (is_string($route->method) && $method !== $route->method)
        ) {
            http_response_code(405);
            header('Allow: ' . (is_array($route->method) ? implode(', ', $route->method) : $route->method));
            include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '405.html';
            exit;
        }

        $action = $route->action;

        if (!$action) {
            http_response_code(500);
            include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . 'unknown.html';
            exit;
        }

        if ($method === 'POST') {
            if (isset($_POST['csrf'])) {
                try {
                    if (!hash_equals(self::getCsrfToken(), $_POST['csrf'])) {
                        http_response_code(403);
                        include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '403.html';
                        exit;
                    }
                } catch (\Exception) {
                    http_response_code(500);
                    include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . 'unknown.html';
                    exit;
                }
            } else {
                http_response_code(403);
                include VIEW_DIR . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . '403.html';
                exit;
            }

            $data = $_POST;
        } else {
            $data = $_GET;
        }

        if (isset($_SESSION['flash'])) {
            $persistedFlash = [];
            foreach ($_SESSION['flash'] as $key => $value) {
                $hops = $value['hops'] ?? 0;
                if ($hops > 0) $persistedFlash[$key] = ['data' => $value['data'] ?? null, 'hops' => $hops - 1];
            }
            $_SESSION['flash'] = $persistedFlash;
        }

        resolve(Request::class, new Request($route, $method, $data));

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
                exit;
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

    private static function interpolateRouteParameters(string $path, mixed $parameters = null): string {
        if (empty($parameters)) return $path;
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                if (is_int($key)) {
                    $path = substr($path, 0, strpos($path, '{')) . $value . substr($path, strpos($path, '}') + 1);
                } else {
                    $path = str_replace('{' . $key . '}', strval($value), $path);
                }
            }
        } else {
            $path = substr($path, 0, strpos($path, '{')) . $parameters . substr($path, strpos($path, '}') + 1);
        }
        return $path;
    }

    /**
     * @param string $name Route name.
     * @param mixed $parameters Route parameters (for example, `.../{id}`).
     * @return string The route's URL.
     */
    public static function route(string $name, mixed $parameters = null): string {
        if (isset(self::$routeAliases[$name])) {
            $aliases = self::$routeAliases[$name];
            if (!is_array($aliases)) {
                if (isset(self::$routes[$aliases])) {
                    /** @var Route $route */
                    $route = self::$routes[$aliases];
                    return self::interpolateRouteParameters($route->path, $parameters);
                }
            } else {
                foreach ($aliases as $alias) {
                    if (isset(self::$routes[$alias])) {
                        /** @var Route $route */
                        $route = self::$routes[$alias];
                        return self::interpolateRouteParameters($route->path, $parameters);
                    }
                }
            }
            return self::interpolateRouteParameters($name, $parameters);
        }
        if (isset(self::$routes[$name])) {
            /** @var Route $route */
            $route = self::$routes[$name];
            return self::interpolateRouteParameters($route->path, $parameters);
        }
        return self::interpolateRouteParameters($name, $parameters);
    }
}