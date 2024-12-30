<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

use Router\Router;

/**
 * @see Router::route()
 */
function route(string $name, mixed $parameters = null): string {
    return Router::route($name, $parameters);
}