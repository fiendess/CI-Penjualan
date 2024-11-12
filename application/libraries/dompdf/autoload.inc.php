<?php
// autoload.inc.php

// Check if Composer's autoloader exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // If Composer autoloader is missing, load classes manually
    require_once __DIR__ . '/src/Autoloader.php';
    Dompdf\Autoloader::register();
}
