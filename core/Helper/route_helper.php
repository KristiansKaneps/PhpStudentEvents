<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Router\Router;

/**
 * @see Router::route()
 */
function route(string $name): string {
    return Router::route($name);
}