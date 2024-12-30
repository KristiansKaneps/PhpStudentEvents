<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Services\Auth;

function auth(): bool {
    return Auth::auth();
}

function user(): ?array {
    return Auth::user();
}

function userId(): ?int {
    return Auth::userId();
}

function isAdmin(): bool {
    return Auth::adminRole();
}

function isOrganizer(): bool {
    return Auth::organizerRole();
}

function isUser(): bool {
    return Auth::userRole();
}

function isGuest(): bool {
    return Auth::guestRole();
}
