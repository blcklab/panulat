<?php

declare(strict_types=1);

$env = static function (string $key, mixed $default = null): mixed {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return $value === false ? $default : $value;
};

$bool = static function (mixed $value, bool $default = false): bool {
    if ($value === null || $value === '') {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
};

return [
    // In production, fail fast when optimized cache files are missing.
    'require_cached_config' => $bool($env('PANULAT_REQUIRE_CACHED_CONFIG'), true),
    'require_cached_routes' => $bool($env('PANULAT_REQUIRE_CACHED_ROUTES'), true),
    'require_cached_container' => $bool($env('PANULAT_REQUIRE_CACHED_CONTAINER'), true),

    // Resolve global middleware once during boot in production.
    'pre_resolve_global_middleware' => $bool($env('PANULAT_PRE_RESOLVE_GLOBAL_MIDDLEWARE'), true),

    // Add Content-Length in the emitter when possible.
    'emit_content_length' => $bool($env('PANULAT_EMIT_CONTENT_LENGTH'), true),
];
