<?php

declare(strict_types=1);

namespace Panulat\Tests\Integration;

use Panulat\Foundation\Application;
use Panulat\Http\Request;
use Panulat\Resource\OffsetPaginator;
use Panulat\Resource\ResourceCollection;
use PHPUnit\Framework\TestCase;

final class PaginatedEndpointTest extends TestCase
{
    public function testPaginatedListShape(): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $app->router()->get('/v1/items', static function (Request $request) {
            $paginator = new OffsetPaginator([['id' => 1]], total: 2, limit: 1, offset: 0, path: '/v1/items');
            return (new ResourceCollection($paginator->items, static fn (array $row): array => $row, $paginator->meta(), $paginator->links()))->response();
        });

        $response = $app->handle(Request::fromServer([
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.test',
            'REQUEST_URI' => '/v1/items',
        ]));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('"meta"', $response->getBody()->getContents());
        self::assertStringContainsString('"links"', $response->getBody()->getContents());
    }
}
