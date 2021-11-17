<?php

declare(strict_types=1);

namespace App\Tests;

use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractAPITestCase extends WebTestCase
{

    /** @var KernelBrowser|null  */
    public ?KernelBrowser $client = null;

    /** @var string|null  */
    private ?string $token = null;

    /**
     *
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @throws JsonException
     */
    public function loginAsStandardUser(
        string $username = 'user@mail.com',
        string $password = '123qweQWE',
    ): void
    {
        $this->sendLoginRequest(
            $username,
            $password
        );
    }

    /**
     * @param string $username
     * @param string $password
     * @throws JsonException
     */
    private function sendLoginRequest(
        string $username,
        string $password
    ): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $username,
                'password' => $password,
            ], JSON_THROW_ON_ERROR)
        );

        $this->token = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['token'] ?? null;

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $this->token));
    }

}