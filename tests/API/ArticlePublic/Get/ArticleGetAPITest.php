<?php

declare(strict_types=1);

namespace App\Tests\API\ArticlePublic\Get;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ArticleGetAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testGetSuccess(): void
    {
        $this->loginAsStandardUser();

        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $article */
        $article = $articleRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/public/articles/' . $article->getId()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['id']);
        self::assertNotEmpty($response['title']);
        self::assertArrayHasKey('price', $response);
        self::assertIsBool($response['urgent']);
        self::assertIsBool($response['fixed']);
        self::assertIsBool($response['negotiable']);
        self::assertArrayHasKey('conditions', $response);
        self::assertArrayHasKey('telephone', $response);
        self::assertIsBool($response['discontinued']);
        self::assertArrayHasKey('description', $response);
        self::assertNotEmpty($response['createdAt']);
        self::assertIsInt($response['hits']);
    }

    /**
     * @throws \JsonException
     */
    public function testGetSuccessNotLoggedIn(): void
    {
        $articleRepo = static::getContainer()->get(ArticleRepository::class);
        /** @var Article $article */
        $article = $articleRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'GET',
            '/api/public/articles/' . $article->getId()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['id']);
        self::assertNotEmpty($response['title']);
        self::assertArrayHasKey('price', $response);
        self::assertIsBool($response['urgent']);
        self::assertIsBool($response['fixed']);
        self::assertIsBool($response['negotiable']);
        self::assertArrayHasKey('conditions', $response);
        self::assertArrayHasKey('telephone', $response);
        self::assertIsBool($response['discontinued']);
        self::assertArrayHasKey('description', $response);
        self::assertNotEmpty($response['createdAt']);
        self::assertIsInt($response['hits']);
    }

    /**
     *
     */
    public function testGetFailedArticleNotFound(): void
    {
        $this->client->request(
            'GET',
            '/api/public/articles/0'
        );

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

}