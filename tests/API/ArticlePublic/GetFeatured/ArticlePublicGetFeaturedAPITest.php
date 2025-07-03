<?php

declare(strict_types=1);

namespace App\Tests\API\ArticlePublic\GetFeatured;

use App\Tests\AbstractAPITestCase;
use Symfony\Component\HttpFoundation\Response;

class ArticlePublicGetFeaturedAPITest extends AbstractAPITestCase
{

    /**
     * @throws \JsonException
     */
    public function testGetFeaturedSuccess(): void
    {
        $this->client->request(
            'GET',
            '/api/public/articles/featured?sort=a.title&direction=asc'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

}