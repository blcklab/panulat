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

$int = static function (mixed $value, int $default = 0): int {
    if ($value === null || $value === '' || filter_var($value, FILTER_VALIDATE_INT) === false) {
        return $default;
    }

    return (int) $value;
};

return [
    // Request logging is opt-in. Keep it disabled for high-throughput APIs unless you need it.
    'requests' => $bool($env('LOG_REQUESTS'), false),

    // Prefer stderr for Docker/FPM production logs. Use "file" only when you explicitly want local file logs.
    // Supported: stderr | file | none
    'channel' => $string($env('LOG_CHANNEL'), 'stderr'),
    'path' => $string($env('LOG_PATH'), dirname(__DIR__) . '/storage/logs/app.log'),
    'max_bytes' => $int($env('LOG_MAX_BYTES'), 5242880),

    // File locking is safer for shared log files but slower under throughput. Keep off unless required.
    'lock' => $bool($env('LOG_LOCK'), false),
];
