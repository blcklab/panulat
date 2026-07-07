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

$origins = array_values(array_filter(array_map('trim', explode(',', $string($env('CORS_ALLOWED_ORIGINS'), '*')))));

return [
    'enabled' => $bool($env('CORS_ENABLED'), true),
    'allowed_origins' => $origins === [] ? ['*'] : $origins,
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-API-Key', 'X-Requested-With', 'X-Request-Id'],
    'credentials' => $bool($env('CORS_CREDENTIALS'), false),
];
