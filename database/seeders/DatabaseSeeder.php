<?php

declare(strict_types=1);

namespace Database\Seeders;

use Panulat\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
    }
}
