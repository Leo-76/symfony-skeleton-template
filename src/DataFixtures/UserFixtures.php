<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFullName('Admin User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Regular user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFullName('Regular User');
        $user->setIsVerified(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        $manager->flush();
    }
}
