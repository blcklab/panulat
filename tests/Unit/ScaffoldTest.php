<?php

declare(strict_types=1);

namespace Panulat\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ScaffoldTest extends TestCase
{
    public function testStarterHasExpectedFolders(): void
    {
        $root = dirname(__DIR__, 2);

        self::assertDirectoryExists($root . '/app');
        self::assertDirectoryExists($root . '/app/Middleware');
        self::assertDirectoryExists($root . '/routes');
        self::assertDirectoryExists($root . '/config');
        self::assertDirectoryExists($root . '/database/migrations');
        self::assertDirectoryExists($root . '/database/seeders');
        self::assertDirectoryExists($root . '/tests');
        self::assertFileExists($root . '/public/index.php');
    }

    public function testStarterHasDeveloperFriendlyAssets(): void
    {
        $root = dirname(__DIR__, 2);

        self::assertFileExists($root . '/README.md');
        self::assertFileExists($root . '/Dockerfile');
        self::assertFileExists($root . '/docker-compose.yml');
        self::assertFileExists($root . '/scripts/setup-sqlite.sh');
        self::assertFileExists($root . '/scripts/test-api.sh');
        self::assertFileExists($root . '/app/Controllers/AuthController.php');
        self::assertFileExists($root . '/app/Controllers/WelcomeController.php');
        self::assertFileExists($root . '/app/Controllers/HealthController.php');
        self::assertFileExists($root . '/app/Middleware/RequestIdMiddleware.php');
        self::assertFileExists($root . '/app/Controllers/MeController.php');
    }

    public function testEnvironmentExampleUsesFriendlyDatabaseVariables(): void
    {
        $env = file_get_contents(dirname(__DIR__, 2) . '/.env.example');

        self::assertIsString($env);
        self::assertStringContainsString('DB_HOST=', $env);
        self::assertStringContainsString('DB_PORT=', $env);
        self::assertStringContainsString('DB_DATABASE=', $env);
        self::assertStringContainsString('DB_USERNAME=', $env);
        self::assertStringContainsString('DB_PASSWORD=', $env);
    }
    public function testFrontControllerNeverEnablesDebugInProduction(): void
    {
        $index = file_get_contents(dirname(__DIR__, 2) . '/public/index.php');

        self::assertIsString($index);
        self::assertStringContainsString("\$environment !== 'production'", $index);
        self::assertStringContainsString('new ErrorHandler($debug)', $index);
    }

}
