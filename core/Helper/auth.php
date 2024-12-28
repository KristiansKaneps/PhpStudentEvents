<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Services\Auth;

function auth(): bool {
    return Auth::auth();
}

function user(): ?array {
    return Auth::user();
}
