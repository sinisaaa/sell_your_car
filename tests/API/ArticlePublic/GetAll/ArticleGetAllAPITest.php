<?php

declare(strict_types=1);

namespace App\Tests\API\ArticlePublic\GetAll;

use App\Tests\AbstractAPITestCase;
use Symfony\Component\HttpFoundation\Response;

class ArticleGetAllAPITest extends AbstractAPITestCase
{

    /**
     * @throws \JsonException
     */
    public function testGetAllSuccess(): void
    {
        $this->client->request(
            'GET',
            '/api/public/articles?sort=a.title&direction=asc'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

    /**
     * @throws \JsonException
     */
    public function testGetUrgentSuccess(): void
    {
        $urgentFilter = [['field' => 'a.urgent', 'type' => 'eq', 'value' => 1]];
        $urgentStringFilter = json_encode($urgentFilter, JSON_THROW_ON_ERROR);

        $this->client->request(
            'GET',
            '/api/public/articles?filters=' . urlencode($urgentStringFilter)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

    /**
     * @throws \JsonException
     */
    public function testGetFixedSuccess(): void
    {
        $urgentFilter = [['field' => 'a.fixed', 'type' => 'eq', 'value' => 1]];
        $urgentStringFilter = json_encode($urgentFilter, JSON_THROW_ON_ERROR);

        $this->client->request(
            'GET',
            '/api/public/articles?filters=' . urlencode($urgentStringFilter)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

    /**
     * @throws \JsonException
     */
    public function testGetNegotiableSuccess(): void
    {
        $urgentFilter = [['field' => 'a.negotiable', 'type' => 'eq', 'value' => 1]];
        $urgentStringFilter = json_encode($urgentFilter, JSON_THROW_ON_ERROR);

        $this->client->request(
            'GET',
            '/api/public/articles?filters=' . urlencode($urgentStringFilter)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

    /**
     * @throws \JsonException
     */
    public function testGetFeaturedPendingSuccess(): void
    {
        $urgentFilter = [['field' => 'a.featuredPending', 'type' => 'eq', 'value' => 1]];
        $urgentStringFilter = json_encode($urgentFilter, JSON_THROW_ON_ERROR);

        $this->client->request(
            'GET',
            '/api/public/articles?filters=' . urlencode($urgentStringFilter)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

    /**
     * @throws \JsonException
     */
    public function testGetDiscontinuedSuccess(): void
    {
        $urgentFilter = [['field' => 'a.discontinued', 'type' => 'eq', 'value' => 1]];
        $urgentStringFilter = json_encode($urgentFilter, JSON_THROW_ON_ERROR);

        $this->client->request(
            'GET',
            '/api/public/articles?filters=' . urlencode($urgentStringFilter)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }
}