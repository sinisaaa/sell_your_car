<?php

declare(strict_types=1);

namespace App\Tests\API\UserPublic\GetUserDetail;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use Symfony\Component\HttpFoundation\Response;

class UserPublicGetUserDetailAPITest extends AbstractAPITestCase
{

    /**
     *
     */
    public function testGetUserDetailSuccess(): void
    {
        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'user@mail.com'])[0];

        $this->client->request(
            'GET',
            '/api/public/users/' . $user->getId()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertArrayHasKey('id', $response);
        self::assertNotEmpty($response['id']);
        self::assertArrayHasKey('name', $response);
        self::assertNotEmpty($response['name']);
        self::assertArrayHasKey('username', $response);
        self::assertNotEmpty( $response['username']);
        self::assertArrayHasKey('phone', $response);
        self::assertArrayHasKey('address', $response);
        self::assertArrayHasKey('roles', $response);
        self::assertGreaterThan(0,  $response['roles']);
        self::assertArrayHasKey('location', $response);
        self::assertArrayHasKey('type', $response);
        self::assertNotEmpty( $response['type']);
    }

    /**
     *
     */
    public function testGetUserDetailFailedUserNotFound(): void
    {
        $this->client->request(
            'GET',
            '/api/public/users/0'
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }


}