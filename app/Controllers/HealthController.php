<?php

declare(strict_types=1);

namespace App\Controllers;

use Panulat\Config\ConfigRepository;
use Panulat\Database\Connection;
use Panulat\Http\Controller\Controller;
use Panulat\Http\Request;
use Panulat\Http\Response;
use Throwable;

final readonly class HealthController extends Controller
{
    public function __construct(
        private Connection $database,
        private ConfigRepository $config,
    ) {
    }

    public function health(Request $request): Response
    {
        return $this->json([
            'data' => [
                'status' => 'ok',
                'service' => (string) $this->config->get('app.name', 'Panulat'),
                'message' => 'Panulat is alive.',
                'request_id' => $request->getAttribute('request_id'),
            ],
        ]);
    }

    public function ready(Request $request): Response
    {
        $checks = [
            'app' => 'ok',
            'database' => 'ok',
            'config' => 'ok',
        ];
        $status = 'ready';
        $httpStatus = 200;

        try {
            $this->database->select('SELECT 1 AS ok');
        } catch (Throwable $throwable) {
            $checks['database'] = 'failed';
            $status = 'not_ready';
            $httpStatus = 503;
        }

        return $this->json([
            'data' => [
                'status' => $status,
                'service' => (string) $this->config->get('app.name', 'Panulat'),
                'message' => $status === 'ready'
                    ? 'Panulat is ready to handle API traffic.'
                    : 'Panulat is alive, but one or more dependencies are not ready.',
                'checks' => $checks,
                'request_id' => $request->getAttribute('request_id'),
            ],
        ], $httpStatus);
    }
}
