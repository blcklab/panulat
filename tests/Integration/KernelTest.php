<?php

declare(strict_types=1);

namespace Panulat\Tests\Integration;

use Panulat\Foundation\Application;
use Panulat\Http\Request;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    public function testWelcomeRouteReturnsPanulatBanner(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/',
            'REMOTE_ADDR' => '127.0.0.1',
        ]));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('text/plain', $response->getHeaderLine('content-type'));
        
        $body = $response->getBody()->getContents();

        self::assertStringContainsString('Panulat API', $body);
        self::assertStringContainsString('API Base    : /v1', $body);
    }

    public function testHealthRouteReturnsJson(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/health',
            'REMOTE_ADDR' => '127.0.0.1',
        ]));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/json', $response->getHeaderLine('content-type'));
        self::assertNotSame('', $response->getHeaderLine('x-request-id'));
    }

    public function testReadyRouteChecksDependencies(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/ready',
            'REMOTE_ADDR' => '127.0.0.1',
        ]));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('"status":"ready"', $response->getBody()->getContents());
        self::assertStringContainsString('"database":"ok"', $response->getBody()->getContents());
    }
}

