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
    'default' => $string($env('PANULAT_LOCALE'), $string($env('APP_LOCALE'), 'en')),
    'fallback' => 'en',
    'supported' => ['en', 'fil'],
];
