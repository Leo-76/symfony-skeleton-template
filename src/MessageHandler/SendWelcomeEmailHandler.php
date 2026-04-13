<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendWelcomeEmail;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendWelcomeEmailHandler
{
    public function __construct(
        private readonly UserRepository  $users,
        private readonly MailerService   $mailer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendWelcomeEmail $message): void
    {
        $user = $this->users->find($message->userId);

        if (null === $user) {
            $this->logger->warning('SendWelcomeEmail: user not found', ['id' => $message->userId]);
            return;
        }

        $this->mailer->sendWelcome($user);

        $this->logger->info('Welcome email sent', ['email' => $user->getEmail()]);
    }
}
