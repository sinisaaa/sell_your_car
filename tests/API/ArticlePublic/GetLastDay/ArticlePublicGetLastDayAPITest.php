<?php

declare(strict_types=1);

namespace App\Tests\API\ArticlePublic\GetLastDay;

use App\Tests\AbstractAPITestCase;
use Symfony\Component\HttpFoundation\Response;

final class ArticlePublicGetLastDayAPITest extends AbstractAPITestCase
{

    /**
     * @throws \JsonException
     */
    public function testGetLastDaySuccess(): void
    {
        $this->client->request(
            'GET',
            '/api/public/articles/last-day?sort=a.title&direction=asc'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
    }

}