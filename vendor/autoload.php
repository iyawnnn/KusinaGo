<?php
// Load global helper functions first
require_once __DIR__ . '/mongodb/src/functions.php';

// Register PSR-4 autoloader manually for MongoDB
spl_autoload_register(function ($class) {
    $prefix = 'MongoDB\\';
    $base_dir = __DIR__ . '/mongodb/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
