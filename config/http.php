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

return [
    // Request bodies are read lazily. This limit is enforced before and after the body is read.
    'max_body_bytes' => $int($env('HTTP_MAX_BODY_BYTES'), 1048576),
];
