<?php

declare(strict_types=1);

namespace App\CommandBus\Article\Create;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ArticleCreateCommand
{

    /**
     * ArticleCreateCommand constructor.
     * @param Article $article
     * @param UploadedFile[] $images
     * @param array<mixed> $fields
     */
    public function __construct(
        private Article $article,
        private array $images,
        private array $fields
    )
    {
    }

    /**
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }

    /**
     * @return UploadedFile[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

}