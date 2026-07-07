<?php

declare(strict_types=1);

$env = static function (string $key, mixed $default = null): mixed {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return $value === false ? $default : $value;
};

$int = static function (mixed $value, int $default = 0): int {
    if ($value === null || $value === '' || filter_var($value, FILTER_VALIDATE_INT) === false) {
        return $default;
    }

    return (int) $value;
};

$defaultMax = $int($env('RATE_LIMIT_MAX_ATTEMPTS'), 60);
$defaultWindow = $int($env('RATE_LIMIT_DECAY_SECONDS'), 60);

return [
    'max_attempts' => $defaultMax,
    'window_seconds' => $defaultWindow,

    'profiles' => [
        'api' => [
            'max_attempts' => $int($env('RATE_LIMIT_API_MAX_ATTEMPTS'), $defaultMax),
            'window_seconds' => $int($env('RATE_LIMIT_API_DECAY_SECONDS'), $defaultWindow),
        ],
        'login' => [
            'max_attempts' => $int($env('RATE_LIMIT_LOGIN_MAX_ATTEMPTS'), 5),
            'window_seconds' => $int($env('RATE_LIMIT_LOGIN_DECAY_SECONDS'), 60),
        ],
    ],
];
