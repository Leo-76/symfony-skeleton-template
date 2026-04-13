<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomepageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body');
    }

    public function testHealthCheck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('checks', $data);
        $this->assertArrayHasKey('timestamp', $data);
    }
}
