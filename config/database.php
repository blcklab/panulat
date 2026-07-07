<?php

declare(strict_types=1);

$root = dirname(__DIR__);

$env = static function (string $key, mixed $default = null): mixed {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return $value === false ? $default : $value;
};

$string = static function (mixed $value, string $default = ''): string {
    if ($value === null || $value === false) {
        return $default;
    }

    $value = trim((string) $value);

    return $value === '' ? $default : $value;
};


$bool = static function (mixed $value, bool $default = false): bool {
    if ($value === null || $value === false) {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
};

$path = static function (string $path) use ($root): string {
    if ($path === ':memory:' || str_starts_with($path, '/')) {
        return $path;
    }

    return rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
};

$sqliteDatabase = $path($string($env('DB_DATABASE'), 'database/database.sqlite'));
$mysqlHost = $string($env('DB_HOST'), '127.0.0.1');
$mysqlPort = $string($env('DB_PORT'), '3306');
$mysqlDatabase = $string($env('DB_DATABASE'), 'panulat');
$mysqlCharset = $string($env('DB_CHARSET'), 'utf8mb4');

return [
    'default' => $string($env('DB_CONNECTION'), 'sqlite'),
    'log_queries' => $bool($env('DB_QUERY_LOG'), false),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => $sqliteDatabase,
            'dsn' => $string($env('DB_DSN'), 'sqlite:' . $sqliteDatabase),
            'username' => null,
            'password' => null,
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => $mysqlHost,
            'port' => $mysqlPort,
            'database' => $mysqlDatabase,
            'username' => $string($env('DB_USERNAME'), 'root'),
            'password' => $string($env('DB_PASSWORD'), ''),
            'charset' => $mysqlCharset,
            'dsn' => $string(
                $env('DB_DSN'),
                sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $mysqlHost,
                    $mysqlPort,
                    $mysqlDatabase,
                    $mysqlCharset,
                ),
            ),
        ],
    ],
];
