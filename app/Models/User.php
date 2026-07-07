<?php

declare(strict_types=1);

namespace App\Models;

use Panulat\Database\Model;

final readonly class User extends Model
{
    protected function table(): string
    {
        return 'users';
    }

    /** @return list<string> */
    protected function columns(): array
    {
        return ['id', 'name', 'email', 'role', 'created_at', 'updated_at'];
    }

    /** @return list<string> */
    protected function fillable(): array
    {
        return ['name', 'email', 'password_hash', 'role'];
    }

    /** @return array<string, mixed>|null */
    public function findByEmail(string $email): ?array
    {
        return $this->query()
            ->select(['id', 'name', 'email', 'password_hash', 'role', 'created_at', 'updated_at'])
            ->where('email', '=', $email)
            ->first();
    }

    /**
     * @param array{name: mixed, email: mixed, password: mixed, role?: mixed} $data
     * @return array<string, mixed>
     */
    public function createWithPassword(array $data): array
    {
        return $this->create([
            'name' => (string) $data['name'],
            'email' => (string) $data['email'],
            'password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
            'role' => (string) ($data['role'] ?? 'user'),
        ]);
    }

    /** @return list<array<string, mixed>> */
    public function allWithProfiles(int $limit = 50, int $offset = 0): array
    {
        return $this->query()
            ->select([
                'users.id as id',
                'users.name as name',
                'users.email as email',
                'users.role as role',
                'users.created_at as created_at',
                'users.updated_at as updated_at',
                'profiles.id as profile_id',
                'profiles.avatar as profile_avatar',
                'profiles.bio as profile_bio',
            ])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->orderBy('users.id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}
