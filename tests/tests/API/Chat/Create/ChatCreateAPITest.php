<?php

declare(strict_types=1);

namespace App\Tests\API\Chat\Create;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ChatCreateAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testCreateSuccess(): void
    {
        $this->loginAsStandardUser();

        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'subject' => 'Test subject',
                'body' => 'Test body',
                'receiver' => $user->getId(),
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['subject']);
        self::assertNotEmpty($response['sender']);
        self::assertNotEmpty($response['receiver']);
    }

    /**
     * @return array
     */
    public function formDataValidationErrors_DP(): array
    {
        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        return [
            [['body' => 'Test body', 'receiver' => $user->getId()], 'No subject'],
            [['subject' => 'Test subject', 'receiver' => $user->getId()], 'No body'],
            [['subject' => 'Test subject', 'body' => 'Test body'], 'No receiver id'],
            [['subject' => 'Test subject', 'body' => 'Test body', 'receiver' => 0], 'Invalid receiver id'],
            [['subject' => null, 'body' => 'Test body', 'receiver' => $user->getId()], 'Subject is null'],
            [['subject' => '', 'body' => 'Test body', 'receiver' => $user->getId()], 'Subject is empty string'],
            [['subject' => 'Test subject', 'body' => null, 'receiver' => $user->getId()], 'Body is null'],
            [['subject' => 'Test subject', 'body' => '', 'receiver' => $user->getId()], 'Body is empty string'],

        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testCreateFailedInvalidReceiverId(array $formData): void
    {
        $this->loginAsStandardUser();
        $this->client->request(
            'POST',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($formData, JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testCreateFailedNotLoggedIn(): void
    {
        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/chats',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'subject' => 'Test subject',
                'body' => 'Test body',
                'receiver' => $user->getId(),
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

}