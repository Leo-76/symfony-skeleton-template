<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Service\MailerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

final class MailerServiceTest extends TestCase
{
    private MailerInterface $mailer;
    private MailerService   $service;

    protected function setUp(): void
    {
        $this->mailer  = $this->createMock(MailerInterface::class);
        $this->service = new MailerService($this->mailer, 'My App', 'noreply@test.com');
    }

    private function makeUser(): User
    {
        $u = new User();
        $u->setEmail('user@example.com');
        $u->setFullName('Test User');
        $u->setPassword('x');
        $u->setIsVerified(true);
        return $u;
    }

    public function testSendWelcomeDispatchesEmail(): void
    {
        $this->mailer->expects($this->once())->method('send');
        $this->service->sendWelcome($this->makeUser());
    }

    public function testSendPasswordResetDispatchesEmail(): void
    {
        $this->mailer->expects($this->once())->method('send');
        $this->service->sendPasswordReset($this->makeUser(), 'fake-token-abc123');
    }

    public function testSendEmailVerificationDispatchesEmail(): void
    {
        $this->mailer->expects($this->once())->method('send');
        $this->service->sendEmailVerification($this->makeUser(), 'verify-token-xyz');
    }
}
