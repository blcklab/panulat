<?php

declare(strict_types=1);

namespace Database\Seeders;

use Panulat\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $existing = $this->table('users')
            ->select(['id'])
            ->where('email', '=', 'avelino@example.test')
            ->first();

        if ($existing !== null) {
            return;
        }

        $userId = $this->table('users')->insertGetId([
            'name' => 'Avelino',
            'email' => 'avelino@example.test',
            'password_hash' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'admin',
        ]);

        $this->table('profiles')->insert([
            'user_id' => $userId,
            'avatar' => 'https://example.com/avatar.png',
            'bio' => 'API developer using Panulat.',
        ]);
    }
}
