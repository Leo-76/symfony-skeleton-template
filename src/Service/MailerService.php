<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

final class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string          $appName,
        private readonly string          $senderEmail = 'noreply@example.com',
    ) {
    }

    /**
     * Send a welcome email to a newly registered user.
     */
    public function sendWelcome(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->appName))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Welcome to ' . $this->appName)
            ->htmlTemplate('emails/welcome.html.twig')
            ->context(['user' => $user]);

        $this->mailer->send($email);
    }

    /**
     * Send a password reset email.
     */
    public function sendPasswordReset(User $user, string $resetToken): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->appName))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Reset your password — ' . $this->appName)
            ->htmlTemplate('emails/password_reset.html.twig')
            ->context([
                'user'       => $user,
                'resetToken' => $resetToken,
                'expiresAt'  => new \DateTimeImmutable('+1 hour'),
            ]);

        $this->mailer->send($email);
    }

    /**
     * Send an email verification link.
     */
    public function sendEmailVerification(User $user, string $verificationToken): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->appName))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Verify your email — ' . $this->appName)
            ->htmlTemplate('emails/email_verification.html.twig')
            ->context([
                'user'              => $user,
                'verificationToken' => $verificationToken,
            ]);

        $this->mailer->send($email);
    }
}
