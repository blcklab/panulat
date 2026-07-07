<?php

declare(strict_types=1);

namespace App\Providers;

use App\Middleware\RequestIdMiddleware;
use Panulat\Container\Container;
use Panulat\Foundation\Application;
use Panulat\Foundation\ServiceProviderInterface;

final readonly class AppServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Bind application services here, for example:
        // $container->singleton(UserService::class, UserService::class);
    }

    public function boot(Application $app): void
    {
        // Every response gets an X-Request-Id header for tracing API calls.
        $app->middleware(RequestIdMiddleware::class);

        // Route middleware groups keep route files clean and cache-friendly.
        $app->middlewareGroup('api', [
            'throttle:api',
        ]);

        $app->middlewareGroup('protected', [
            'auth',
        ]);
    }
}
