<?php

declare(strict_types=1);

namespace App\Tests\API\Article\Sell;

use App\Repository\ArticleRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ArticleSellAPITest extends AbstractAPITestCase
{

    /**
     *
     */
    public function testSellFailedNotLoggedIn(): void
    {
        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testSell'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/sell'
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testSellFailedNoAccessToArticle(): void
    {
        $this->loginAsStandardUser();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testSellOtherUser'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/sell'
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testSellSuccessAdminUser(): void
    {
        $this->loginAsAdmin();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testSellOtherUser'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/sell'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($response['soldAt']);
    }

    /**
     * @throws JsonException
     */
    public function testSellSuccessStandardUserOnOwnArticle(): void
    {
        $this->loginAsAdmin();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testSell'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/sell'
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($response['soldAt']);
    }

    /**
     * @throws JsonException
     */
    public function testSellFailedArticleIsDeactived(): void
    {
        $this->loginAsAdmin();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testDeactivate'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/sell'
        );

        self::assertSame(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

}