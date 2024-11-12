<?php
// Autoload classes from the dompdf library
spl_autoload_register(function ($class) {
    $prefix = 'Dompdf\\';
    $base_dir = __DIR__ . '/src/';

    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Include the file if it exists
    if (file_exists($file)) {
        require $file;
    }
});
