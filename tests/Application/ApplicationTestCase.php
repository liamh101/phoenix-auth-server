<?php

namespace App\Tests\Application;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class ApplicationTestCase extends WebTestCase
{
    use Factories;

    public KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function createUser(): User
    {
        // password
        return UserFactory::createOne(['email' => 'test@test.com', 'password' => '$2y$04$Lr.wEaRw9FNZ6RiPk9nGqOlhJCY49BUn55UlEK0r3Xh4DxgGhctC2'])->_real();
    }

    protected function createAuthenticatedClient(): KernelBrowser
    {
        $this->createUser();
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'test@test.com',
                'password' => 'password',
            ],
                JSON_THROW_ON_ERROR)
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $this->client;
    }
}
