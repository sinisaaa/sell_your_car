<?php

declare(strict_types=1);

namespace App\Tests\API\Admin\AdminUser\GetAll;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AdminUsersGetAllAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetAllSuccess(): void
    {
        $this->loginAsAdmin();

        $this->client->request(
            'GET',
            '/api/admin/users?perPage=10&page=1&sort=u.name&direction=asc&filters=%5B%7B%22label%22%3A%22User+Name%22%2C%22field%22%3A%22u.name%22%2C%22type%22%3A%22eq%22%2C%22value%22%3A%22Regular+User%22%7D%5D',
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertCount(1, $response['items']);
        self::assertNotEmpty($response['_meta']);
    }

    /**
     * @throws JsonException
     */
    public function testGetAllFailedStandardUser(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'GET',
            '/api/admin/users?perPage=10&page=1&sort=u.name&direction=asc&filters=%5B%7B%22label%22%3A%22User+Name%22%2C%22field%22%3A%22u.name%22%2C%22type%22%3A%22eq%22%2C%22value%22%3A%22Regular+User%22%7D%5D',
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     */
    public function testGetAllFailedNotLoggedIn(): void
    {
        $this->client->request(
            'GET',
            '/api/admin/users?perPage=10&page=1&sort=u.name&direction=asc&filters=%5B%7B%22label%22%3A%22User+Name%22%2C%22field%22%3A%22u.name%22%2C%22type%22%3A%22eq%22%2C%22value%22%3A%22Regular+User%22%7D%5D',
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}