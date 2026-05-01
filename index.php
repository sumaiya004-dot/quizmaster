<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
define('CORE_PATH', __DIR__ . '/Core');
define('BASE_URL', 'http://localhost/quizmaster');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => CORE_PATH . '/',
        'App\\Controllers\\' => APP_PATH . '/Controllers/',
        'App\\Models\\' => APP_PATH . '/Models/',
    ];

    foreach ($prefixes as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $dir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

require_once APP_PATH . '/helpers.php';

$router = new Core\Router();
$router->dispatch();
