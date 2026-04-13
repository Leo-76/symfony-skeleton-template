<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Example voter — extend this pattern for domain-specific access control.
 *
 * Usage in a controller:
 *   $this->denyAccessUnlessGranted(UserVoter::EDIT, $targetUser);
 */
final class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const EDIT = 'USER_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT], true)
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        /** @var User $subject */
        return match ($attribute) {
            self::VIEW => $this->canView($subject, $currentUser),
            self::EDIT => $this->canEdit($subject, $currentUser),
            default    => false,
        };
    }

    private function canView(User $subject, User $currentUser): bool
    {
        // Admins can view anyone; users can only view themselves
        return in_array('ROLE_ADMIN', $currentUser->getRoles(), true)
            || $subject->getId()->equals($currentUser->getId());
    }

    private function canEdit(User $subject, User $currentUser): bool
    {
        // Only the user themselves or an admin can edit
        return in_array('ROLE_ADMIN', $currentUser->getRoles(), true)
            || $subject->getId()->equals($currentUser->getId());
    }
}
