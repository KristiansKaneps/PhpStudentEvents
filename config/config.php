<?php
global $env;
$env = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env');

/**
 * Gets the config property by key.
 * @param string $key Config property.
 * @param string|null $defaultValue Default value if config property is not found or its value is `null`.
 * @return string|null Config property's value.
 */
function config(string $key, string | null $defaultValue = null): string | null {
    global $env;
    return $env[strtoupper(str_replace('.', '_', $key))] ?? $defaultValue;
}
