<?php

/**
 * PSR-4 Autoloader for HRManagement namespace
 */
spl_autoload_register(function ($class) {
    // HRManagement namespace
    $prefix = 'HRManagement\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
