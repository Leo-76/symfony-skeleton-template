<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/api', name: 'api_')]
class MeController extends AbstractApiController
{
    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(#[CurrentUser] User $user): JsonResponse
    {
        return $this->success([
            'id'         => (string) $user->getId(),
            'email'      => $user->getEmail(),
            'fullName'   => $user->getFullName(),
            'roles'      => $user->getRoles(),
            'isVerified' => $user->isVerified(),
            'createdAt'  => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'lastLoginAt'=> $user->getLastLoginAt()?->format(\DateTimeInterface::ATOM),
        ]);
    }
}
