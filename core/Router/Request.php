<?php

namespace Router;

use DI\Dependency;

class Request implements Dependency {
    protected readonly Route $route;
    public readonly string $method;
    public array $data;

    public function __construct(Route $route, string $method, #[\SensitiveParameter] array $data) {
        $this->route = $route;
        $this->method = $method;
        $this->data = $data;
    }

    public function getRoute(): Route { return $this->route; }

    public function isPost(): bool {
        return $this->method === 'POST';
    }

    public function isGet(): bool {
        return $this->method === 'GET';
    }

    public function isset(string $key): bool {
        return isset($this->data[$key]);
    }

    public function empty(string $key): bool {
        return !isset($this->data[$key]) || empty(trim($this->data[$key]));
    }

    public function has(string $key): bool {
        return !$this->empty($key);
    }

    public function input(string $key, mixed $defaultValue = null): mixed {
        return isset($this->data[$key]) ? trim($this->data[$key]) : $defaultValue;
    }

    public function retainInput(string|array $keys): array {
        return array_filter($this->data, is_array($keys)
            ? function ($key) use ($keys) { return in_array($key, $keys); }
            : function ($key) use ($keys) { return $key === $keys; }, ARRAY_FILTER_USE_KEY);
    }

    public function removeInput(string|array $keys): array {
        return array_filter($this->data, is_array($keys)
            ? function ($key) use ($keys) { return !in_array($key, $keys); }
            : function ($key) use ($keys) { return $key !== $keys; }, ARRAY_FILTER_USE_KEY);
    }

    public function setInput(string $key, mixed $value): void {
        $this->data[$key] = trim($value);
    }
}