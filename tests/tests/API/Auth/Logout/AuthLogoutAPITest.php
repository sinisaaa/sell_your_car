<?php

declare(strict_types=1);

namespace App\Tests\API\Auth\Logout;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AuthLogoutAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testLoginSuccess(): void
    {
        $this->loginAsStandardUser('logout@mail.com');

        $this->client->request(
            'POST',
            '/api/logout'
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     */
    public function testLoginFailedUnauthorized(): void
    {
        $this->client->request(
            'POST',
            '/api/logout'
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

}