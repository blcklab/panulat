<?php

declare(strict_types=1);

namespace App\Controllers;

use Panulat\Config\ConfigRepository;
use Panulat\Http\Controller\Controller;
use Panulat\Http\Response;
use Panulat\Support\AsciiBanner;

final readonly class WelcomeController extends Controller
{
    public function __construct(private ConfigRepository $config)
    {
    }

    public function index(): Response
    {
        return Response::text($this->welcomeText());
    }

    private function welcomeText(): string
    {
        $service = $this->stringConfig('app.name', 'Panulat API');
        $description = $this->stringConfig('app.description', 'Fast, clean REST APIs.');
        $environment = $this->stringConfig('app.env', 'local');
        $banner = class_exists(AsciiBanner::class) ? AsciiBanner::render($service) : [$service];

        return implode(PHP_EOL, [
            '',
            ...$banner,
            '',
            $service,
            $description,
            '',
            'Status      : OK',
            'Environment : ' . $environment,
            'Framework   : Panulat',
            'API Base    : /v1',
            '',
            'Health      : GET /v1/health',
            'Readiness   : GET /v1/ready',
            '',
        ]);
    }

    private function stringConfig(string $key, string $default): string
    {
        $value = $this->config->get($key, $default);

        return is_string($value) && trim($value) !== '' ? trim($value) : $default;
    }
}
