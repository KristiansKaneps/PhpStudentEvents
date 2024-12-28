<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

function session(string $key, ?string $defaultValue): mixed {
    return $_SESSION[$key] ?? $defaultValue;
}
