<?php

declare(strict_types=1);

namespace App\Tests\API\Auth\ForgotPassword;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AuthForgotPasswordAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testForgotPasswordSuccess(): void
    {
        $this->client->request(
            'POST',
            '/api/forgot-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'user@mail.com',
            ], JSON_THROW_ON_ERROR)
        );


        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testForgotPasswordSuccessInactiveUser(): void
    {
        $this->client->request(
            'POST',
            '/api/forgot-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'inactive@mail.com',
            ], JSON_THROW_ON_ERROR)
        );


        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testForgotPasswordSuccessNotExistingUser(): void
    {
        $this->client->request(
            'POST',
            '/api/forgot-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'notExisting@mail.com',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     *
     */
    public function testForgotPasswordFailedEmailNotSend(): void
    {
        $this->client->request(
            'POST',
            '/api/forgot-password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }
}