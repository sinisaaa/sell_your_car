<?php

declare(strict_types=1);

namespace App\Tests\API\CarDealer\DeleteRating;

use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class CarDealerDeleteRatingAPITest extends AbstractAPITestCase
{

    /**
     *
     */
    public function testDeleteRatingFailedNotLoggedIn(): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealerForRatings@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'DELETE',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
        );
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testDeleteRatingSuccess(): void
    {
        $this->loginAsStandardUser();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealerForRatings@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'DELETE',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotEmpty($response['id']);
    }

    /**
     * @throws JsonException
     */
    public function testDeleteRatingFailedAlreadyDeleted(): void
    {
        $this->loginAsStandardUser();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealerForRatings@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'DELETE',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

}