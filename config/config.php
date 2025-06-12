<?php
// Global configuration settings
session_start();

// Site configuration
define('SITE_NAME', 'Quiz Test System');
define('SITE_URL', 'http://localhost');

// Admin credentials (simple authentication)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');

// Include database configuration
require_once 'database.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        '../models/',
        '../controllers/',
        'models/',
        'controllers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});
?>
