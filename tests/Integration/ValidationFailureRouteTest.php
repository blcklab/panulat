<?php

declare(strict_types=1);

namespace Panulat\Tests\Integration;

use Panulat\Foundation\Application;
use Panulat\Http\Request;
use Panulat\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class ValidationFailureRouteTest extends TestCase
{
    public function testValidationFailureIsProblemJson(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $app->router()->post('/v1/validate', static function (Request $request): array {
            Validator::make($request->json(), ['email' => 'required|email'])->validate();
            return ['data' => ['ok' => true]];
        });

        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'POST',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/validate',
            'CONTENT_TYPE' => 'application/json',
        ], '{"email":"bad"}'));

        self::assertSame(422, $response->getStatusCode());
        self::assertStringContainsString('validation-error', $response->getBody()->getContents());
    }
}
