<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Router\Router;

function csrf(): string {
    return Router::getCsrfToken() ?? '';
}
