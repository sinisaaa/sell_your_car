<?php

declare(strict_types=1);

namespace App\Tests\API\Region\GetAll;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class RegionGetAllAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetAllSuccess(): void
    {
        $this->client->request(
            'GET',
            '/api/public/regions'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
    }

}