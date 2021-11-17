<?php


namespace App\Tests\API\Auth\Login;


use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AuthLoginAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testLoginSuccess(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@mail.com',
                'password' => '123qweQWE',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['token']);
        self::assertNotEmpty($response['refreshToken']);
    }

    /**
     * @throws JsonException
     */
    public function testLoginFailedFieldsNotSent(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testLoginFailedInvalidEmail(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'not-existing-email@mail.com',
                'password' => '123qweQWE',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        self::assertSame($response['detail'], 'Invalid credentials');
    }

    /**
     * @throws JsonException
     */
    public function testLoginFailedInvalidPassword(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@mail.com',
                'password' => 'invalidPassword',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        self::assertSame($response['detail'], 'Invalid credentials');
    }
}