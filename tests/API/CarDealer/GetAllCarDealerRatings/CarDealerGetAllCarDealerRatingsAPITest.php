<?php

declare(strict_types=1);

namespace App\Tests\API\CarDealer\GetAllCarDealerRatings;

use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class CarDealerGetAllCarDealerRatingsAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetAllSuccess(): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $carDealer = $userRepo->findBy(['email' => 'carDealer@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/public/car-dealers/' .$carDealer->getId(). '/ratings?sort=ur.rating&direction=asc'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertIsArray($response['items']);
        self::assertIsArray($response['_meta']);
    }

}