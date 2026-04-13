<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
final class LoginSuccessListener
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->setLastLoginAt(new \DateTimeImmutable());
        $this->userRepository->getEntityManager()->flush();
    }
}
