<?php

declare(strict_types=1);

namespace Panulat\Tests\Integration;

use Panulat\Jwt\JwtMiddleware;
use Panulat\Jwt\JwtService;
use Panulat\Foundation\Application;
use Panulat\Http\Request;
use PHPUnit\Framework\TestCase;

final class AuthProtectedRouteTest extends TestCase
{
    public function testJwtProtectedRoute(): void
    {
        $jwt = new JwtService('secret');
        $app = Application::boot(dirname(__DIR__, 2));
        $app->router()->get('/v1/protected', static fn (): array => ['data' => ['ok' => true]], [new JwtMiddleware($jwt)]);

        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/protected',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $jwt->encode(['sub' => '1', 'exp' => time() + 60]),
        ]));

        self::assertSame(200, $response->getStatusCode());
    }
}
