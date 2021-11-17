<?php

declare(strict_types=1);

namespace App\Tests\API\Region\GetLocations;

use App\Entity\Region;
use App\Repository\RegionRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class RegionsGetLocationsAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetLocationsSuccess(): void
    {
        $this->loginAsStandardUser();

        $regionRepo = static::getContainer()->get(RegionRepository::class);
        /** @var Region $region */
        $region = $regionRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/regions/' . $region->getId() . '/locations'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['items']);
        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testGetLocationsFailedRegionNotExist(): void
    {
        $this->loginAsStandardUser();

        $regionRepo = static::getContainer()->get(RegionRepository::class);
        /** @var Region $region */
        $region = $regionRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/regions/0/locations'
        );

        self::assertNotNull($this->client->getResponse());
        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
    /**
     * @throws JsonException
     */
    public function testGetLocationsFailedUnauthorized(): void
    {
        $regionRepo = static::getContainer()->get(RegionRepository::class);
        /** @var Region $region */
        $region = $regionRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/regions/' . $region->getId() . '/locations'
        );

        self::assertNotNull($this->client->getResponse());
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }


}