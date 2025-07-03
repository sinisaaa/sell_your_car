<?php

declare(strict_types=1);

namespace App\Tests\API\Admin\AdminUser\AdminUserPromote;

use App\Helper\ValueObjects\RoleCode;
use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AdminUserPromoteAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testPromoteSuccess(): void
    {
        $this->loginAsAdmin();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userForPromote = $userRepo->findBy(['email' => 'userForPromote@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/users/' . $userForPromote->getId() . '/promote',
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertCount(2, $response['roles']);
        self::assertContains(RoleCode::CAR_DEALER, $response['roles']);
    }

    /**
     * @throws JsonException
     */
    public function testPromoteFailedNotAdmin(): void
    {
        $this->loginAsStandardUser();

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userForPromote = $userRepo->findBy(['email' => 'userForPromote@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/users/' . $userForPromote->getId() . '/promote',
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     */
    public function testPromoteFailedNotLoggedIn(): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        $userForPromote = $userRepo->findBy(['email' => 'userForPromote@mail.com'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/users/' . $userForPromote->getId() . '/promote',
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}