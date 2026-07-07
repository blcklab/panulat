<?php

declare(strict_types=1);

namespace App\Controllers;

use Panulat\Auth\TokenUser;
use Panulat\Foundation\Exception\UnauthorizedException;
use Panulat\Http\Controller\Controller;
use Panulat\Http\Request;
use Panulat\Http\Response;

final readonly class MeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->getAttribute('user');

        if (! $user instanceof TokenUser) {
            throw new UnauthorizedException();
        }

        return $this->json([
            'data' => [
                'id' => $user->getAuthIdentifier(),
                'claims' => $user->claims(),
            ],
        ]);
    }
}
