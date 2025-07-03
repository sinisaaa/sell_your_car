<?php

declare(strict_types=1);

namespace App\Tests\API\Chat\Remove;

use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ChatRemoveAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testRemoveSuccess(): void
    {
        $this->loginAsStandardUser();

        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'user@mail.com'])[0];

        $chatRepo = static::getContainer()->get(ChatRepository::class);
        /** @var Chat $chat */
        $chat = $chatRepo->findBy(['receiver' => $user, 'sender' => $user], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'DELETE',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'ids' => [$chat->getId()]
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRemoveFailedNoAccessToChat(): void
    {
        $this->loginAsStandardUser();

        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'sender@mail.com'])[0];

        $chatRepo = static::getContainer()->get(ChatRepository::class);
        /** @var Chat $chat */
        $chat = $chatRepo->findBy(['receiver' => $user, 'sender' => $user], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'DELETE',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'ids' => [$chat->getId()]
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRemoveFailedChatNotFound(): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'DELETE',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'ids' => [0]
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testRemoveFailedUnAuthorized(): void
    {
        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy(['email' => 'user@mail.com'])[0];

        $chatRepo = static::getContainer()->get(ChatRepository::class);
        /** @var Chat $chat */
        $chat = $chatRepo->findBy(['receiver' => $user, 'sender' => $user], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'DELETE',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'ids' => [$chat->getId()]
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

}