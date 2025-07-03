<?php

declare(strict_types=1);

namespace App\Tests\API\UserToken\Refresh;

use App\Entity\UserToken;
use App\Repository\UserTokenRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class UserTokenRefreshAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testRefreshTokenSuccess(): void
    {
        $userTokenRepo = static::getContainer()->get(UserTokenRepository::class);
        /** @var UserToken $token */
        $token = $userTokenRepo->findBy([], ['id' => 'DESC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/token/refresh',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'refreshToken' => $token->getRefreshToken()
            ], JSON_THROW_ON_ERROR)
        );

        self::assertNotNull($this->client->getResponse());
        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRefreshTokenFailedTokenNotProvided(): void
    {
        $this->client->request(
            'POST',
            '/api/token/refresh'
        );

        self::assertNotNull($this->client->getResponse());
        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRefreshTokenFailedInvalidTOken(): void
    {
        $this->client->request(
            'POST',
            '/api/token/refresh',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'refreshToken' => 'some-invalid-token'
            ], JSON_THROW_ON_ERROR)
        );

        self::assertNotNull($this->client->getResponse());
        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRefreshTokenFailedTokenExpired(): void
    {
        $userTokenRepo = static::getContainer()->get(UserTokenRepository::class);
        /** @var UserToken $token */
        $token = $userTokenRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/token/refresh',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'refreshToken' => $token->getRefreshToken()
            ], JSON_THROW_ON_ERROR)
        );

        self::assertNotNull($this->client->getResponse());
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

}