<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class HealthController
{
    public function __construct(
        private Connection $connection,
        private CacheInterface $cache,
    ) {
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $checks = [
            'app' => 'ok',
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
        ];

        $healthy = !in_array('error', $checks, true);
        $status = $healthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE;

        return new JsonResponse([
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => $checks,
        ], $status);
    }

    private function checkDatabase(): string
    {
        try {
            $this->connection->executeQuery('SELECT 1');

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }

    private function checkRedis(): string
    {
        try {
            $this->cache->get('health_check', static fn (): string => 'pong');

            return 'ok';
        } catch (\Throwable) {
            return 'error';
        }
    }
}
