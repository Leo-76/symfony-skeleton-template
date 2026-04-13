<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/health', name: 'health_')]
class HealthCheckController extends AbstractController
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    #[Route('', name: 'check', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $status = 'ok';
        $checks = [];

        // Database check
        try {
            $this->connection->executeQuery('SELECT 1');
            $checks['database'] = 'ok';
        } catch (\Throwable $e) {
            $checks['database'] = 'error';
            $status = 'degraded';
        }

        return $this->json([
            'status' => $status,
            'checks' => $checks,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ], $status === 'ok' ? 200 : 503);
    }
}
