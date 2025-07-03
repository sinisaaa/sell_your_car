<?php

declare(strict_types=1);

namespace App\Tests\API\Article\Deactivate;

use App\Repository\ArticleRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ArticleDeactivateAPITest extends AbstractAPITestCase
{

    /**
     *
     */
    public function testDeactivateFailedNotLoggedIn(): void
    {
        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testDeactivateOtherUser'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/deactivate'
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @throws JsonException
     */
    public function testDeactivateFailedNoAccessToArticle(): void
    {
        $this->loginAsStandardUser();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testDeactivateOtherUser'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/deactivate'
        );

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testDeactivateSuccessAdminUser(): void
    {
        $this->loginAsAdmin();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testDeactivateOtherUser'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/deactivate'
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    public function testDeactivateSuccessStandardUserOnOwnArticle(): void
    {
        $this->loginAsAdmin();

        /** @var ArticleRepository $articleRepo */
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        $article = $articleRepo->findBy(['title' => 'testDeactivate'], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles/' . $article->getId() . '/deactivate'
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

}