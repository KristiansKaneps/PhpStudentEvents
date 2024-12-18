<?php
const ROOT_DIR = __DIR__ . DIRECTORY_SEPARATOR;
const PUBLIC_DIR = ROOT_DIR . 'public' . DIRECTORY_SEPARATOR;
const VIEW_DIR = ROOT_DIR . 'views' . DIRECTORY_SEPARATOR;
const CORE_DIR = ROOT_DIR . 'core' . DIRECTORY_SEPARATOR;

require_once ROOT_DIR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

spl_autoload_register(function ($class) {
    // Replace namespace separator with directory separator
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Build the full path
    $file = CORE_DIR . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        if (config('ENVIRONMENT') === 'production') {
            die("Could not autoload files.");
        } else {
            die("The file $file could not be loaded.");
        }
    }
});
