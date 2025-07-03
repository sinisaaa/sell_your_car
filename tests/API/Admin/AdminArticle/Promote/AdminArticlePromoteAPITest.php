<?php

declare(strict_types=1);

namespace App\Tests\API\Admin\AdminArticle\Promote;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class AdminArticlePromoteAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testPromoteSuccessAlreadyPromoted(): void
    {
        $this->loginAsAdmin();

        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $articleForPromote */
        $articleForPromote = $articleRepo->findBy(['title' => 'test featured'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/articles/' . $articleForPromote->getId() . '/promote',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'numberOfDays' => '3',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertTrue( $response['featured']);
    }

    /**
     * @throws JsonException
     */
    public function testPromoteSuccess(): void
    {
        $this->loginAsAdmin();

        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $articleForPromote */
        $articleForPromote = $articleRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/articles/' . $articleForPromote->getId() . '/promote',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'numberOfDays' => '3',
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertTrue( $response['featured']);
    }

    /**
     * @throws JsonException
     */
    public function testPromoteFailedArticleNotFound(): void
    {
        $this->loginAsAdmin();

        $this->client->request(
            'POST',
            '/api/admin/articles/0/promote',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'numberOfDays' => '3',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testPromoteFailedInvalidPromotionPeriod(): void
    {
        $this->loginAsAdmin();

        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $articleForPromote */
        $articleForPromote = $articleRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/articles/' . $articleForPromote->getId() . '/promote',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'numberOfDays' => '0',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testPromoteFailedNotAdmin(): void
    {
        $this->loginAsStandardUser();

        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $articleForPromote */
        $articleForPromote = $articleRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/articles/' . $articleForPromote->getId() . '/promote',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'numberOfDays' => '3',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testPromoteFailedNotLoggedIn(): void
    {
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $articleForPromote */
        $articleForPromote = $articleRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/admin/articles/' . $articleForPromote->getId() . '/promote',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'numberOfDays' => '3',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

}