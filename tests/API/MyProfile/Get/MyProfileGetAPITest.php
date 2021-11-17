<?php

declare(strict_types=1);

namespace App\Tests\API\MyProfile\Get;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class MyProfileGetAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetMyProfileSuccess(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'GET',
            '/api/my-profile'
        );

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
    }

    /**
     * @throws JsonException
     */
    public function testGetMyProfileFailedNotLoggedIn(): void
    {
        $this->client->request(
            'GET',
            '/api/my-profile'
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

}