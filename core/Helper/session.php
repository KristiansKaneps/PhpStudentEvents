<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

function session(string $key, ?string $defaultValue): mixed {
    return $_SESSION[$key] ?? $defaultValue;
}

function old(string $key, mixed $defaultValue = null): mixed {
    if (isset($_SESSION['flash']))
        return $_SESSION['flash'][$key] ?? $defaultValue;
    return $defaultValue;
}

function has(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

function toasts(string $type): array {
    return $_SESSION['flash']['toast'][$type] ?? [];
}
