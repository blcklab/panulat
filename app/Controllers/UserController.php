<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Resources\UserResource;
use Panulat\Foundation\Exception\NotFoundException;
use Panulat\Http\Controller\Controller;
use Panulat\Http\Request;
use Panulat\Http\Response;
use Panulat\Resource\OffsetPaginator;
use Panulat\Resource\ResourceCollection;

final readonly class UserController extends Controller
{
    public function __construct(private User $users)
    {
    }

    public function index(Request $request): Response
    {
        $limit = min(100, max(1, (int) $request->query('limit', 20)));
        $offset = max(0, (int) $request->query('offset', 0));
        $items = $this->users->all($limit, $offset);
        $paginator = new OffsetPaginator(
            items: $items,
            total: $this->users->count(),
            limit: $limit,
            offset: $offset,
            path: '/v1/users',
        );

        return (new ResourceCollection(
            items: $items,
            transformer: static fn (array $user): array => (new UserResource($user))->toArray(),
            meta: $paginator->meta(),
            links: $paginator->links(),
        ))->response();
    }

    public function withProfiles(Request $request): Response
    {
        $limit = min(100, max(1, (int) $request->query('limit', 20)));
        $offset = max(0, (int) $request->query('offset', 0));
        $items = $this->users->allWithProfiles($limit, $offset);
        $paginator = new OffsetPaginator(
            items: $items,
            total: $this->users->count(),
            limit: $limit,
            offset: $offset,
            path: '/v1/users/with-profiles',
        );

        return (new ResourceCollection(
            items: $items,
            transformer: static fn (array $user): array => (new UserResource($user))->toArray(),
            meta: $paginator->meta(),
            links: $paginator->links(),
        ))->response();
    }

    public function show(Request $request): Response
    {
        $user = $this->users->find((string) $request->getAttribute('id'));

        if ($user === null) {
            throw new NotFoundException('User was not found.');
        }

        return (new UserResource($user))->response();
    }

    public function store(Request $request): Response
    {
        $validated = $this->validate($request, [
            'name' => 'required|string|min:2|max:80',
            'email' => 'required|email|max:120',
        ]);

        return (new UserResource($this->users->create($validated)))->response(201);
    }

    public function update(Request $request): Response
    {
        $validated = $this->validate($request, [
            'name' => 'required|string|min:2|max:80',
            'email' => 'required|email|max:120',
        ]);

        $user = $this->users->updateById((string) $request->getAttribute('id'), $validated);

        if ($user === null) {
            throw new NotFoundException('User was not found.');
        }

        return (new UserResource($user))->response();
    }

    public function destroy(Request $request): Response
    {
        if (! $this->users->deleteById((string) $request->getAttribute('id'))) {
            throw new NotFoundException('User was not found.');
        }

        return $this->deleted();
    }
}
