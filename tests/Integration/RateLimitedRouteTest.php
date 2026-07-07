<?php

declare(strict_types=1);

namespace Panulat\Tests\Integration;

use Panulat\Cache\ArrayCache;
use Panulat\Foundation\Application;
use Panulat\Http\Request;
use Panulat\RateLimit\RateLimiter;
use Panulat\RateLimit\RateLimitMiddleware;
use PHPUnit\Framework\TestCase;

final class RateLimitedRouteTest extends TestCase
{
    public function testRouteCanBeRateLimited(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $middleware = new RateLimitMiddleware(new RateLimiter(new ArrayCache()), 1, 60);
        $app->router()->get('/v1/limited', static fn (): array => ['data' => ['ok' => true]], [$middleware]);

        $server = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/limited',
            'REMOTE_ADDR' => '10.0.0.1',
        ];

        self::assertSame(200, $app->handle(Request::fromServer($server))->getStatusCode());
        self::assertSame(429, $app->handle(Request::fromServer($server))->getStatusCode());
    }

    public function testNamedThrottleMiddlewareCanBeUsedOnRoutes(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $app->throttle('test', 1, 60);
        $app->router()->get('/v1/limited-named', static fn (): array => ['data' => ['ok' => true]], ['throttle:test']);

        $server = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/limited-named',
            'REMOTE_ADDR' => '10.0.0.2',
        ];

        self::assertSame(200, $app->handle(Request::fromServer($server))->getStatusCode());
        self::assertSame(429, $app->handle(Request::fromServer($server))->getStatusCode());
    }

    public function testMiddlewareGroupsCanBeUsedOnRoutes(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $app->throttle('group-test', 1, 60);
        $app->middlewareGroup('limited-group', ['throttle:group-test']);
        $app->router()->get('/v1/limited-group', static fn (): array => ['data' => ['ok' => true]], ['limited-group']);

        $server = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/limited-group',
            'REMOTE_ADDR' => '10.0.0.3',
        ];

        self::assertSame(200, $app->handle(Request::fromServer($server))->getStatusCode());
        self::assertSame(429, $app->handle(Request::fromServer($server))->getStatusCode());
    }
}
