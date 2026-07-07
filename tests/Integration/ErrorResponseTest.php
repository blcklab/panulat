<?php

declare(strict_types=1);

namespace Panulat\Tests\Integration;

use Panulat\Foundation\Application;
use Panulat\Http\Request;
use PHPUnit\Framework\TestCase;

final class ErrorResponseTest extends TestCase
{
    public function testNotFoundIsJsonProblem(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/missing',
        ]));

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('not-found', $response->getBody()->getContents());
    }
}
