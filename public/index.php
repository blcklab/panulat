<?php

declare(strict_types=1);

use Panulat\Config\Env;
use Panulat\Foundation\Application;
use Panulat\Foundation\ErrorHandler;
use Panulat\Http\Emitter;

require dirname(__DIR__) . '/vendor/autoload.php';

$basePath = dirname(__DIR__);

if (class_exists(Env::class)) {
    (new Env())->load($basePath . '/.env');
}

$environment = strtolower((string) ($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: 'local'));
$debugValue = $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? getenv('APP_DEBUG');
$debug = $environment !== 'production'
    && (filter_var($debugValue, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false);

try {
    Application::boot($basePath)->run();
} catch (Throwable $throwable) {
    (new Emitter())->emit((new ErrorHandler($debug))->render($throwable));
}
