<?php

declare(strict_types=1);

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
    if ($value === null || $value === '') {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
};

return [
    'name' => $string($env('APP_NAME'), 'Panulat API'),
    'description' => $string($env('APP_DESCRIPTION'), 'Fast, clean REST APIs.'),
    'env' => $string($env('APP_ENV'), 'local'),
    'debug' => $bool($env('APP_DEBUG'), true),
    'providers' => [
        Panulat\Jwt\JwtServiceProvider::class,
        App\Providers\AppServiceProvider::class,
    ],
];
