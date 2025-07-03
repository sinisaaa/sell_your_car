<?php

declare(strict_types=1);

namespace App\Tests\API\Chat\GetAllForUser;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ChatGetAllForUserAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetAllSuccess(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'GET',
            '/api/chats?perPage=10&page=1',
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
    }

    /**
     * @throws JsonException
     */
    public function testGetAllFailedUnauthorized(): void
    {
        $this->client->request(
            'GET',
            '/api/chats',
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }


}