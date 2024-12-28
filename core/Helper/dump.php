<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

function dd(mixed $value, mixed ...$values): void {
    echo print_r($value, true) . "<br>";
    foreach ($values as $value)
        echo print_r($value, true) . "<br>";
    die();
}
