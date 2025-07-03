<?php

declare(strict_types=1);

namespace App\Tests\API\MyProfile\Update;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class MyProfileUpdateAPITest extends AbstractAPITestCase
{

    /**
     * @throws \JsonException
     */
    public function testUpdateSuccess(): void
    {
        $this->loginAsStandardUser();

        $locationRepo = static::getContainer()->get(LocationRepository::class);
        /** @var Location $location */
        $location = $locationRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'PUT',
            '/api/my-profile',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Updated Name',
                'location' => $location->getId(),
                'address' => 'Updated Address 16',
                'phone' => '0112335489',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertSame($response['name'], 'Updated Name');
        self::assertSame($response['address'], 'Updated Address 16');
        self::assertSame($response['location']['city'], $location->getCity());
        self::assertSame($response['phone'], '0112335489');
    }

    /**
     * @return array
     */
    public function formDataValidationErrors_DP(): array
    {
        return [
            [['name' => 'Update User', 'email' => 'test@asd.com'], Response::HTTP_BAD_REQUEST, 'Email can not be changed'],
            [['name' => 'Updated User', 'plainPassword' => '123qweQWE'], Response::HTTP_BAD_REQUEST, 'Password can not be changed'],
            [['name' => 'Updated User', 'someExtraField' => '123qweQWE'], Response::HTTP_BAD_REQUEST, 'Extra fields not allowed'],
            [['name' => '', ], Response::HTTP_BAD_REQUEST, 'Name can not be blank'],
            [['name' => null, ], Response::HTTP_BAD_REQUEST, 'Name can not be blank'],
        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testUpdateValidationFailed(mixed $formData, int $responseCode): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'PUT',
            '/api/my-profile',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($formData, JSON_THROW_ON_ERROR)
        );

        self::assertSame($responseCode, $this->client->getResponse()->getStatusCode());
    }

}