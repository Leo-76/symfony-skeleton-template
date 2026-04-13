<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileControllerTest extends WebTestCase
{
    public function testProfileRedirectsWhenNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profile');

        $this->assertResponseRedirects('/login');
    }

    public function testProfileShowsForAuthenticatedUser(): void
    {
        $client    = static::createClient();
        $container = static::getContainer();

        // Create a user programmatically
        $em   = $container->get('doctrine')->getManager();
        $user = new User();
        $user->setEmail('test-profile@example.com');
        $user->setFullName('Test User');
        $user->setPassword(
            $container->get(UserPasswordHasherInterface::class)->hashPassword($user, 'password'),
        );
        $user->setIsVerified(true);
        $em->persist($user);
        $em->flush();

        $client->loginUser($user);
        $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Test User');
    }
}
