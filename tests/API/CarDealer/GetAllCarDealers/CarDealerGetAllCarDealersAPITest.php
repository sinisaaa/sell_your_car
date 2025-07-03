<?php

declare(strict_types=1);

namespace App\Tests\API\CarDealer\GetAllCarDealers;

use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class CarDealerGetAllCarDealersAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetAllSuccess(): void
    {
        $this->client->request(
            'GET',
            '/api/public/car-dealers?sort=u.email&direction=asc'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertNotEmpty($response['_meta']);
        self::assertGreaterThan(0, count($response['items']));
    }

}