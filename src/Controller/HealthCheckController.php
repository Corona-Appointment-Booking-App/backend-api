<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractApiController
{
    #[Route('/api/health-check', name: 'api.health-check', methods: ['GET'])]
    public function healthCheck(): Response
    {
        return $this->json(['success' => true]);
    }
}
