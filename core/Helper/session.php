<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

function session(string $key, ?string $defaultValue): mixed {
    return $_SESSION[$key] ?? $defaultValue;
}

function old(string $key, mixed $defaultValue = null): mixed {
    if (isset($_SESSION['flash'][$key]))
        return $_SESSION['flash'][$key]['data'] ?? $defaultValue;
    return $defaultValue;
}

function has(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

function toasts(): array {
    $notificationService = resolve(\Services\NotificationService::class);
    return $notificationService->listToastNotifications();
}
