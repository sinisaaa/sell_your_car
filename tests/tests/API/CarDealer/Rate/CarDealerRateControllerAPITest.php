<?php

declare(strict_types=1);

namespace App\Tests\API\CarDealer\Rate;

use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class CarDealerRateControllerAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testRateFailedNotLoggedIn(): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealer@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 5,
                'comment' => 'Some test comment'
            ],
                JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRateFailedUserIsNotCarDealer(): void
    {
        $this->loginAsStandardUser();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'sender@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 5,
                'comment' => 'Some test comment'
            ],
                JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRateFailedCarDealerRateHimself(): void
    {
        $this->loginAsCarDealer();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealer@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 5,
                'comment' => 'Some test comment'
            ],
                JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function formDataValidationErrors_DP(): array
    {
        return [
            [['comment' => 'Some comment'], Response::HTTP_BAD_REQUEST, 'No rating'],
            [['rate' => null, 'comment' => 'Some comment'], Response::HTTP_BAD_REQUEST, 'Rating is null'],
            [['rate' => 6, 'comment' => 'Some comment'], Response::HTTP_BAD_REQUEST, 'Rating is greater than 5'],
        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testRateFailedValidationError(mixed $formData, int $responseCode): void
    {
        $this->loginAsCarDealer();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealer@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($formData,
                JSON_THROW_ON_ERROR)
        );

        self::assertSame($responseCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRateSuccess(): void
    {
        $this->loginAsStandardUser();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealer@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 5,
                'comment' => 'Some test comment'
            ],
                JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotEmpty($response['id']);
    }

    /**
     * @throws JsonException
     */
    public function testRateFailedUserAlreadyRatedDealer(): void
    {
        $this->loginAsStandardUser();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userToRate = $userRepo->findBy(['email' => 'carDealer@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/car-dealers/' . $userToRate->getId() . '/rate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 5,
                'comment' => 'Some test comment'
            ],
                JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

}
