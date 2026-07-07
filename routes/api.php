<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HealthController;
use App\Controllers\MeController;
use App\Controllers\UserController;
use App\Controllers\WelcomeController;
use Panulat\Routing\Router;

/** @var Router $router */
$router->get('/', [WelcomeController::class, 'index']);

$router->group('/v1', function (Router $router): void {
    $router->get('/health', [HealthController::class, 'health']);
    $router->get('/ready', [HealthController::class, 'ready']);

    $router->post('/auth/register', [AuthController::class, 'register']);
    $router->post('/auth/login', [AuthController::class, 'login'], ['throttle:login']);
    $router->get('/me', MeController::class, ['protected']);

    $router->group('/users', function (Router $router): void {
        $router->get('/', [UserController::class, 'index']);
        $router->post('/', [UserController::class, 'store']);
        $router->get('/with-profiles', [UserController::class, 'withProfiles']);
        $router->get('/{id:\\d+}', [UserController::class, 'show']);
        $router->put('/{id:\\d+}', [UserController::class, 'update']);
        $router->delete('/{id:\\d+}', [UserController::class, 'destroy']);
    }, ['api']);
});
