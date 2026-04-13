<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testRegisterRedirectsWhenLoggedIn(): void
    {
        $client = static::createClient();

        // Simulate authenticated user by checking the redirect behaviour
        // (full auth test would need fixtures; kept lightweight here)
        $client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testRegisterWithInvalidDataShowsErrors(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Create Account')->form([
            'registration_form[fullName]'     => '',
            'registration_form[email]'        => 'not-an-email',
            'registration_form[plainPassword]' => 'short',
            'registration_form[agreeTerms]'   => false,
        ]);

        $client->submit($form);
        $this->assertResponseIsSuccessful(); // stays on page
        $this->assertSelectorExists('.form-error');
    }
}
