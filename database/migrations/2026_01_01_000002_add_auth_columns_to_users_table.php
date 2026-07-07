<?php

declare(strict_types=1);

use Panulat\Database\Connection;

return static function (Connection $connection): void {
    $hasColumn = static function (Connection $connection, string $column): bool {
        if ($connection->driverName() === 'mysql') {
            $row = $connection->select(
                'SELECT COUNT(*) AS aggregate FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
                ['table' => 'users', 'column' => $column],
            )[0] ?? ['aggregate' => 0];

            return (int) $row['aggregate'] > 0;
        }

        foreach ($connection->select('PRAGMA table_info(users)') as $row) {
            if (($row['name'] ?? null) === $column) {
                return true;
            }
        }

        return false;
    };

    if (! $hasColumn($connection, 'password_hash')) {
        $connection->statement($connection->driverName() === 'mysql'
            ? 'ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NULL'
            : 'ALTER TABLE users ADD COLUMN password_hash TEXT NULL');
    }

    if (! $hasColumn($connection, 'role')) {
        $connection->statement($connection->driverName() === 'mysql'
            ? "ALTER TABLE users ADD COLUMN role VARCHAR(40) NOT NULL DEFAULT 'user'"
            : "ALTER TABLE users ADD COLUMN role TEXT NOT NULL DEFAULT 'user'");
    }
};
