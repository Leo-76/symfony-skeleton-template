<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\Voter\UserVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class UserVoterTest extends TestCase
{
    private UserVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new UserVoter();
    }

    private function makeUser(array $roles = []): User
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFullName('Test');
        $user->setPassword('x');
        $user->setRoles($roles);
        return $user;
    }

    private function token(User $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken($user, 'main', $user->getRoles());
    }

    public function testOwnerCanViewThemselves(): void
    {
        $user  = $this->makeUser();
        $token = $this->token($user);

        $result = $this->voter->vote($token, $user, [UserVoter::VIEW]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testOwnerCanEditThemselves(): void
    {
        $user  = $this->makeUser();
        $token = $this->token($user);

        $result = $this->voter->vote($token, $user, [UserVoter::EDIT]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testAdminCanEditAnyUser(): void
    {
        $admin  = $this->makeUser(['ROLE_ADMIN']);
        $target = $this->makeUser();
        $token  = $this->token($admin);

        $result = $this->voter->vote($token, $target, [UserVoter::EDIT]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserCannotEditAnotherUser(): void
    {
        $user1 = $this->makeUser();
        $user2 = $this->makeUser();
        $token = $this->token($user1);

        $result = $this->voter->vote($token, $user2, [UserVoter::EDIT]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }
}
