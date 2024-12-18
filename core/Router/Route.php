<?php

namespace Router;

use Closure;

class Route {
    public readonly string|array $method;
    public readonly string $path;

    public readonly string|array $name;
    public readonly Closure|array $action;

    protected array $aliases;
    private readonly string $updateAliasesCallback;

    private static function __getUniqueNameForOneMethod(string $method, string $path): string {
        return $method . '::' . $path;
    }

    public static function __getUniqueName(string|array $method, string $path): string|array {
        if (is_array($method)) {
            return array_map(function ($singleMethod) use ($path) {
                return self::__getUniqueNameForOneMethod($singleMethod, $path);
            }, $method);
        }
        return self::__getUniqueNameForOneMethod($method, $path);
    }

    /**
     * @param string|array $method The request method (can be multiple) (e.g. GET, POST).
     * @param string $path The path (e.g. /, /event/list).
     * @param callable|array $action The action (e.g. `[HomeController::class, 'index']`, function).
     * @param null|string|array $alias The route's alias (can be also assigned with `->name('the alias')`).
     * @param callable-string $updateAliasesCallback Callback for updating the route's aliases within router.
     */
    public function __construct(string|array $method, string $path, callable|array $action, null|string|array $alias, string $updateAliasesCallback) {
        $this->method = $method;
        $this->path = $path;
        $this->action = $action;
        $this->name = self::__getUniqueName($method, $path);
        $this->aliases = is_array($alias) ? $alias : (empty($alias) ? array() : array($alias));
        $this->updateAliasesCallback = $updateAliasesCallback;
    }

    public function getAliases(): array {
        return $this->aliases;
    }

    /**
     * Sets the alias for this route.
     * @param string $alias The main alias.
     * @param string ...$aliases The aliases.
     * @return $this
     */
    public function name(string $alias, string ...$aliases): Route {
        $newAliases = [$alias, ...$aliases];
        Router::{$this->updateAliasesCallback}($this, $this->aliases, $newAliases);
        $this->aliases = $newAliases;
        return $this;
    }

    /**
     * Check if the given URI matches the route's path and extract parameters.
     * @param string $uri
     * @return array|false An associative array of parameters or `false` if no match.
     */
    public function match(string $uri): array|false {
        $pattern = preg_replace('#\{([a-zA-Z0-9_]+)}#', '(?P<$1>[^/]+)', $this->path);
        $pattern = "#^" . $pattern . "$#";
        if (preg_match($pattern, $uri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }
        return false;
    }
}