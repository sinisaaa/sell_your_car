<?php

declare(strict_types=1);

namespace App\Tests\API\Chat\Open;

use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ChatOpenAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testOpenSuccess(): void
    {
        $this->loginAsStandardUser();

        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'user@mail.com'])[0];

        $chatRepo = static::getContainer()->get(ChatRepository::class);
        /** @var Chat $chat */
        $chat = $chatRepo->findBy(['receiver' => $user], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/chats/' . $chat->getId(),
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['sender']);
        self::assertNotEmpty($response['receiver']);
        self::assertTrue($response['seen']);
    }

    /**
     * @throws JsonException
     */
    public function testOpenFailedOthersPeopleChat(): void
    {
        $this->loginAsStandardUser();

        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'sender@mail.com'])[0];

        $chatRepo = static::getContainer()->get(ChatRepository::class);
        /** @var Chat $chat */
        $chat = $chatRepo->findBy(['receiver' => $user, 'sender' => $user], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/chats/' . $chat->getId(),
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testOpenFailedChatNotFound(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'GET',
            '/api/chats/0',
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testOpenFailedUnauthorized(): void
    {
        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'user@mail.com'])[0];

        $chatRepo = static::getContainer()->get(ChatRepository::class);
        /** @var Chat $chat */
        $chat = $chatRepo->findBy(['receiver' => $user], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/chats/' . $chat->getId(),
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }
}