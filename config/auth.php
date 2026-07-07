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

return [
    'jwt' => [
        'secret' => $string($env('JWT_SECRET'), 'change-me'),
        'enforce_production_secret' => true,
    ],
    'api_keys' => $string($env('API_KEYS'), ''),
];
