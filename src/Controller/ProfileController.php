<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/profile', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: 'show', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('profile/show.html.twig', [
            'user' => $this->requireUser(),
        ]);
    }

    #[Route('/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $user = $this->requireUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addSuccess('Profile updated successfully.');

            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
