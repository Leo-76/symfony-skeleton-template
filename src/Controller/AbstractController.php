<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends BaseController
{
    /**
     * Returns the currently authenticated user or null.
     */
    protected function getAuthenticatedUser(): ?User
    {
        $user = $this->getUser();
        return $user instanceof User ? $user : null;
    }

    /**
     * Returns the currently authenticated user or throws an exception.
     */
    protected function requireUser(): User
    {
        $user = $this->getAuthenticatedUser();
        if (null === $user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }
        return $user;
    }

    /**
     * Adds a success flash message.
     */
    protected function addSuccess(string $message): void
    {
        $this->addFlash('success', $message);
    }

    /**
     * Adds an error flash message.
     */
    protected function addError(string $message): void
    {
        $this->addFlash('danger', $message);
    }

    /**
     * Adds a warning flash message.
     */
    protected function addWarning(string $message): void
    {
        $this->addFlash('warning', $message);
    }

    /**
     * Adds an info flash message.
     */
    protected function addInfo(string $message): void
    {
        $this->addFlash('info', $message);
    }

    /**
     * Returns the current page number from the request (min 1).
     */
    protected function getPage(Request $request, string $param = 'page'): int
    {
        return max(1, (int) $request->query->get($param, 1));
    }
}
