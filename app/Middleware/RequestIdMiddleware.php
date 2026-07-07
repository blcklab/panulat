<?php

declare(strict_types=1);

namespace App\Middleware;

use Panulat\Http\Request;
use Panulat\Http\Response;
use Panulat\Middleware\MiddlewareInterface;
use Panulat\Middleware\RequestHandlerInterface;

final class RequestIdMiddleware implements MiddlewareInterface
{
    private const HEADER = 'X-Request-Id';

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $requestId = $this->resolveRequestId($request);
        $response = $handler->handle($request->withAttribute('request_id', $requestId));

        return $response->withHeader(self::HEADER, $requestId);
    }

    private function resolveRequestId(Request $request): string
    {
        $incoming = trim($request->getHeaderLine(self::HEADER));

        if ($this->isSafeRequestId($incoming)) {
            return $incoming;
        }

        return bin2hex(random_bytes(16));
    }

    private function isSafeRequestId(string $value): bool
    {
        if ($value === '' || strlen($value) > 128) {
            return false;
        }

        return preg_match('/^[A-Za-z0-9_.:\\-]+$/', $value) === 1;
    }
}
