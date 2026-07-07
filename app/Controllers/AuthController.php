<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Panulat\Jwt\JwtService;
use Panulat\Foundation\Exception\UnauthorizedException;
use Panulat\Foundation\Exception\ValidationException;
use Panulat\Http\Controller\Controller;
use Panulat\Http\Request;
use Panulat\Http\Response;

final readonly class AuthController extends Controller
{
    public function __construct(
        private User $users,
        private JwtService $jwt,
    ) {
    }

    public function register(Request $request): Response
    {
        $validated = $this->validate($request, [
            'name' => 'required|string|min:2|max:80',
            'email' => 'required|email|max:120',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($this->users->findByEmail((string) $validated['email']) !== null) {
            throw new ValidationException([
                'email' => ['The email has already been registered.'],
            ]);
        }

        $user = $this->users->createWithPassword([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        return $this->json($this->tokenPayload($user), 201);
    }

    public function login(Request $request): Response
    {
        $validated = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = $this->users->findByEmail((string) $validated['email']);

        if ($user === null || ! password_verify((string) $validated['password'], (string) ($user['password_hash'] ?? ''))) {
            throw new UnauthorizedException('Invalid email or password.');
        }

        return $this->json($this->tokenPayload($user));
    }

    /**
     * @param array<string, mixed> $user
     * @return array<string, mixed>
     */
    private function tokenPayload(array $user): array
    {
        $now = time();
        $expiresIn = 3600;
        $token = $this->jwt->encode([
            'sub' => (string) $user['id'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
            'iat' => $now,
            'exp' => $now + $expiresIn,
        ]);

        return [
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $token,
                'expires_in' => $expiresIn,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'] ?? 'user',
                ],
            ],
        ];
    }
}
