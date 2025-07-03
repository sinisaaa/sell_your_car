<?php

declare(strict_types=1);

namespace App\Tests\API\Auth\ConfirmEmail;

use App\Entity\Location;
use App\Entity\UserEmailConfirmToken;
use App\Repository\LocationRepository;
use App\Repository\UserEmailConfirmTokenRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AuthConfirmEmailAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testConfirmSuccess(): void
    {
        /** @var UserEmailConfirmTokenRepository $tokenRepo */
        $tokenRepo = static::getContainer()->get(UserEmailConfirmTokenRepository::class);

        $token = $tokenRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/confirm-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => $token->getToken(),
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['email']);
    }

    /**
     * @throws JsonException
     */
    public function testConfirmFailedTokenNotProvided(): void
    {
        $this->client->request(
            'POST',
            '/api/confirm-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testConfirmFailedInvalidToken(): void
    {
        $this->client->request(
            'POST',
            '/api/confirm-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => 'someInvalidToken',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testConfirmFailedExpiredToken(): void
    {
        /** @var UserEmailConfirmTokenRepository $tokenRepo */
        $tokenRepo = static::getContainer()->get(UserEmailConfirmTokenRepository::class);

        $expiredToken = $tokenRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/confirm-email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => $expiredToken->getToken(),
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }
}