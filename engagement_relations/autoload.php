<?php

// Load shared existing Database implementation
require_once __DIR__ . '/../auth/database.php';

// Simple PSR-4 autoloader for App namespace in engagement_relations
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $parts = explode('\\', $relative_class);
    $root = array_shift($parts);

    $map = [
        'Controllers' => 'controllers/',
        'Models' => 'models/',
    ];

    if (!isset($map[$root])) {
        return;
    }

    $file = __DIR__ . '/' . $map[$root] . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
