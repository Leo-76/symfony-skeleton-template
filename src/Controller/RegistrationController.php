<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Message\SendWelcomeEmail;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly MessageBusInterface         $bus,
    ) {
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserRepository $users): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check uniqueness
            if ($users->findByEmail($user->getEmail())) {
                $this->addError('An account with this email already exists.');

                return $this->render('registration/register.html.twig', ['form' => $form]);
            }

            $user->setPassword(
                $this->hasher->hashPassword($user, $form->get('plainPassword')->getData()),
            );

            $this->em->persist($user);
            $this->em->flush();

            // Dispatch async welcome email
            $this->bus->dispatch(new SendWelcomeEmail((string) $user->getId()));

            $this->addSuccess('Account created! You can now log in.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form,
        ]);
    }
}
