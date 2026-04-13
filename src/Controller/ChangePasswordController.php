<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/profile/password', name: 'app_profile_password_')]
class ChangePasswordController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    #[Route('', name: 'change', methods: ['GET', 'POST'])]
    public function change(Request $request): Response
    {
        $user = $this->requireUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $user->setPassword($this->hasher->hashPassword($user, $newPassword));
            $this->em->flush();

            $this->addSuccess('Password changed successfully.');

            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form,
        ]);
    }
}
