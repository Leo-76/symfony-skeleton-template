<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Pagination\DoctrinePaginator;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'admin_')]
class UserCrudController extends AbstractController
{
    public function __construct(
        private readonly UserRepository         $users,
        private readonly EntityManagerInterface $em,
        private readonly DoctrinePaginator      $paginator,
    ) {
    }

    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $search = $request->query->getString('q');
        $page   = $this->getPage($request);

        $qb = $this->users->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC');

        if ($search !== '') {
            $qb->andWhere('u.email LIKE :q OR u.fullName LIKE :q')
               ->setParameter('q', '%' . $search . '%');
        }

        $pagination = $this->paginator->paginate($qb, $page, 20);

        return $this->render('admin/user/list.html.twig', [
            'pagination' => $pagination,
            'search'     => $search,
        ]);
    }

    #[Route('/users/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }

    #[Route('/users/{id}/toggle-admin', name: 'user_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(User $user, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('toggle-admin-' . $user->getId(), $request->request->getString('_token'))) {
            $this->addError('Invalid CSRF token.');

            return $this->redirectToRoute('admin_user_list');
        }

        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $user->setRoles(array_values(array_filter($roles, fn ($r) => $r !== 'ROLE_ADMIN')));
            $this->addInfo(sprintf('%s is no longer an admin.', $user->getFullName()));
        } else {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_unique($roles));
            $this->addSuccess(sprintf('%s is now an admin.', $user->getFullName()));
        }

        $this->em->flush();

        return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
    }

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->request->getString('_token'))) {
            $this->addError('Invalid CSRF token.');

            return $this->redirectToRoute('admin_user_list');
        }

        // Prevent self-deletion
        if ($user->getId()->equals($this->requireUser()->getId())) {
            $this->addError('You cannot delete your own account.');

            return $this->redirectToRoute('admin_user_list');
        }

        $this->users->remove($user);
        $this->addSuccess(sprintf('User "%s" deleted.', $user->getEmail()));

        return $this->redirectToRoute('admin_user_list');
    }
}
