<?php

declare(strict_types=1);

namespace App\Tests\API\Auth\ChangePassword;

use App\Repository\UserForgotPasswordTokenRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AuthChangePasswordAPITest extends AbstractAPITestCase
{

    /**
     * @return array
     */
    public function formDataValidationErrors_DP(): array
    {
        return [
            [['first' => '123qweQWE', 'second' => '123qweQWE1'], 'Passwords not match'],
            [['first' => 'qweQWEEE', 'second' => 'qweQWEEE'], 'Passwords without number'],
            [['first' => '123qweee', 'second' => '123qweee'], 'Passwords without uppercase'],
            [['first' => '123QWEEE', 'second' => '123QWEEE'], 'Passwords without lowercase'],
            [['first' => '123QWqw', 'second' => '123QWqw'], 'Passwords less than 8 characters'],
            [[], 'Empty password'],
            [['first' => '123qweQWE'], 'Empty repeated password'],
            [['second' => '123qweQWE'], 'Empty repeated password'],
        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testChangePasswordFailedValidationErrors(mixed $passwordData): void
    {
        /** @var UserForgotPasswordTokenRepository $tokenRepo */
        $tokenRepo = static::getContainer()->get(UserForgotPasswordTokenRepository::class);

        $token = $tokenRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => $token->getToken(),
                'password' => $passwordData
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

    }

    /**
     * @throws JsonException
     */
    public function testChangePasswordSuccess(): void
    {
        /** @var UserForgotPasswordTokenRepository $tokenRepo */
        $tokenRepo = static::getContainer()->get(UserForgotPasswordTokenRepository::class);

        $token = $tokenRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => $token->getToken(),
                'password' => ['first' => '123qweQWE', 'second' => '123qweQWE']
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
    public function testChangePasswordNoTokenFailed(): void
    {
        $this->client->request(
            'POST',
            '/api/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'password' => ['first' => '123qweQWE', 'second' => '123qweQWE']
            ], JSON_THROW_ON_ERROR)
        );


        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testChangePasswordInvalidTokenFailed(): void
    {
        $this->client->request(
            'POST',
            '/api/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => 'invalidToken',
                'password' => ['first' => '123qweQWE', 'second' => '123qweQWE']
            ], JSON_THROW_ON_ERROR)
        );


        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testChangePasswordExpiredTokenFailed(): void
    {
        /** @var UserForgotPasswordTokenRepository $tokenRepo */
        $tokenRepo = static::getContainer()->get(UserForgotPasswordTokenRepository::class);

        $expiredToken = $tokenRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/change-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'token' => $expiredToken->getToken(),
                'password' => ['first' => '123qweQWE', 'second' => '123qweQWE']
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }
}