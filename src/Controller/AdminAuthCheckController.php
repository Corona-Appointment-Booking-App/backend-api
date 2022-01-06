<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAuthCheckController extends AbstractApiController
{
    #[Route('/api/admin-auth-check', name: 'api.admin-auth-check', methods: ['GET'])]
    public function adminAuthCheck(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->json(['success' => true]);
    }
}
