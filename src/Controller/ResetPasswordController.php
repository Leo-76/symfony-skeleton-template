<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResetToken;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\ResetTokenRepository;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/password', name: 'app_password_')]
class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly MailerService               $mailer,
    ) {
    }

    /**
     * Step 1 — request a reset link.
     */
    #[Route('/forgot', name: 'forgot', methods: ['GET', 'POST'])]
    public function forgot(Request $request, UserRepository $users, ResetTokenRepository $tokens): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $users->findByEmail($form->get('email')->getData());

            if ($user) {
                // Invalidate previous tokens
                $tokens->invalidateUserTokens($user);

                $resetToken = new ResetToken($user);
                $this->em->persist($resetToken);
                $this->em->flush();

                $this->mailer->sendPasswordReset($user, $resetToken->getToken());
            }

            // Always show the same message to avoid user enumeration
            $this->addInfo('If an account exists for this email, a reset link has been sent.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/forgot.html.twig', ['form' => $form]);
    }

    /**
     * Step 2 — set a new password via the token link.
     */
    #[Route('/reset/{token}', name: 'reset', methods: ['GET', 'POST'])]
    public function reset(string $token, Request $request, ResetTokenRepository $tokens): Response
    {
        $resetToken = $tokens->findValidToken($token);

        if (!$resetToken) {
            $this->addError('This password reset link is invalid or has expired.');

            return $this->redirectToRoute('app_password_forgot');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $resetToken->getUser();
            $user->setPassword($this->hasher->hashPassword($user, $form->get('newPassword')->getData()));

            $resetToken->markAsUsed();
            $this->em->flush();

            $this->addSuccess('Password reset successfully. You can now log in.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'form'  => $form,
            'token' => $token,
        ]);
    }
}
