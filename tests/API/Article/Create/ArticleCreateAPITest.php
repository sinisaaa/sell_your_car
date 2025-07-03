<?php

declare(strict_types=1);

namespace App\Tests\API\Article\Create;

use App\Entity\ArticleCategory;
use App\Repository\ArticleCategoryRepository;
use App\Tests\AbstractAPITestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class ArticleCreateAPITest extends AbstractAPITestCase
{

    /**
     * @throws JsonException
     */
    public function testCreateSuccess(): void
    {
        $this->loginAsStandardUser();

        $image = $this->getImageForUpload();

        $articleCategoryRepo = static::getContainer()->get(ArticleCategoryRepository::class);
        /** @var ArticleCategory $articleCategory */
        $articleCategory = $articleCategoryRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles',
            [],
            ['images' => [$image]],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test title',
                'exchange' => 0,
                'price' => 10.5,
                'urgent' => 1,
                'fixed' => 0,
                'negotiable' => 0,
                'conditions' => 'Novo',
                'telephone' => '0112223332',
                'discontinued' => 1,
                'description' => 'Some long description',
                'category' => $articleCategory->getId()
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertNotNull($this->client->getResponse());
        self::assertNotEmpty($response['id']);
        self::assertSame($response['title'], 'Test title');
        self::assertFalse($response['exchange']);
        self::assertSame($response['price'], 10.5);
        self::assertTrue($response['urgent']);
        self::assertFalse($response['fixed']);
        self::assertFalse($response['negotiable']);
        self::assertSame($response['conditions'], 'Novo');
        self::assertSame($response['telephone'], '0112223332');
        self::assertTrue($response['discontinued']);
        self::assertSame($response['description'], 'Some long description');
        self::assertNotEmpty($response['createdAt']);
        self::assertIsInt($response['hits']);
    }

    /**
     * @throws JsonException
     */
    public function testCreateFailedInvalidImage(): void
    {
        $this->loginAsStandardUser();

        $image = $this->getInvalidImageForUpload();

        $articleCategoryRepo = static::getContainer()->get(ArticleCategoryRepository::class);
        /** @var ArticleCategory $articleCategory */
        $articleCategory = $articleCategoryRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles',
            [],
            ['images' => [$image]],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test title',
                'exchange' => 0,
                'price' => 10.5,
                'urgent' => 1,
                'fixed' => 0,
                'negotiable' => 0,
                'conditions' => 'Novo',
                'telephone' => '0112223332',
                'discontinued' => 1,
                'description' => 'Some long description',
                'category' => $articleCategory->getId()
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @throws JsonException
     */
    public function testCreateFailedNotLoggedIn(): void
    {

        $articleCategoryRepo = static::getContainer()->get(ArticleCategoryRepository::class);
        /** @var ArticleCategory $articleCategory */
        $articleCategory = $articleCategoryRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        $this->client->request(
            'POST',
            '/api/articles',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test title',
                'exchange' => 0,
                'price' => 10.5,
                'urgent' => 1,
                'fixed' => 0,
                'negotiable' => 0,
                'conditions' => 'Novo',
                'telephone' => '0112223332',
                'discontinued' => 1,
                'description' => 'Some long description',
                'category' => $articleCategory->getId()
            ], JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array[]
     */
    public function formDataValidationErrors_DP(): array
    {
        $articleCategoryRepo = static::getContainer()->get(ArticleCategoryRepository::class);
        /** @var ArticleCategory $articleCategory */
        $articleCategory = $articleCategoryRepo->findBy([], ['id' => 'ASC'], 1, 0)[0];

        return [
            [['title' => null, 'category' => $articleCategory->getId()], 'Title is null'],
            [['title' => '', 'category' => $articleCategory->getId()], 'Title is empty'],
            [['category' => $articleCategory->getId()], 'No title'],
            [['title' => 'Some title'], 'No category'],
            [[], 'No params'],
        ];
    }

    /**
     * @throws JsonException
     * @dataProvider formDataValidationErrors_DP
     */
    public function testCreateFailedValidationError(array $formData): void
    {
        $this->loginAsStandardUser();

        $this->client->request(
            'POST',
            '/api/articles',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($formData, JSON_THROW_ON_ERROR)
        );

        self::assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

}