<?php

declare(strict_types=1);

$files = [
    __DIR__ . '/src/Http/Request.php',
    __DIR__ . '/src/Http/Response.php',
    __DIR__ . '/src/Http/Uri.php',
    __DIR__ . '/src/Http/Stream.php',
    __DIR__ . '/src/Foundation/Application.php',
    __DIR__ . '/src/Routing/Router.php',
    __DIR__ . '/src/Container/Container.php',
    __DIR__ . '/src/Middleware/Pipeline.php',
];

foreach ($files as $file) {
    if (is_file($file)) {
        opcache_compile_file($file);
    }
}
