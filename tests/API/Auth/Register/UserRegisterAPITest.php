<?php

declare(strict_types=1);

namespace App\Tests\API\Auth\Register;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterAPITest extends AbstractAPITestCase
{

    /**
     * @return array
     */
    public function formDataValidationErrors_DP(): array
    {
        return [
            [['name' => 'New User', 'plainPassword' => '123qweQWE'], Response::HTTP_BAD_REQUEST, 'No email'],
            [['email' => 'new@mail.com', 'plainPassword' => '123qweQWE'], Response::HTTP_BAD_REQUEST, 'No name'],
            [['email' => 'new@mail.com', 'name' => 'New User'], Response::HTTP_BAD_REQUEST, 'No password'],
        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testCreateValidationFailed(mixed $formData, int $responseCode): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($formData, JSON_THROW_ON_ERROR)
        );

        self::assertSame($responseCode, $this->client->getResponse()->getStatusCode());
    }

    /**
 * @throws JsonException
 */
    public function testCreateSuccess(): void
    {
        $locationRepo = static::getContainer()->get(LocationRepository::class);
        /** @var Location $location */
        $location = $locationRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'new@mail.com',
                'name' => 'New User',
                'location' => $location->getId(),
                'plainPassword' => '123qweQWE',
                'address' => 'Address 16',
                'phone' => '011233548',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['email']);
    }

    /**
     * @throws JsonException
     *
     * There is limit based on ip address. User should wait some time (defined in .env) before sending next request
     */
    public function testCreateFailedAfterLastRegisterSuccess(): void
    {
        $locationRepo = static::getContainer()->get(LocationRepository::class);
        /** @var Location $location */
        $location = $locationRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'new2@mail.com',
                'name' => 'New User',
                'location' => $location->getId(),
                'plainPassword' => '123qweQWE',
                'address' => 'Address 16',
                'phone' => '011233548',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

}