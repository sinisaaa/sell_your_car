<?php

declare(strict_types=1);

namespace App\CommandBus\Article\Update;

use App\Entity\Article;

final class ArticleUpdateCommand
{

    /**
     * ArticleUpdateCommand constructor.
     * @param Article $article
     * @param array $images
     * @param array $fields
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
     * @return array
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