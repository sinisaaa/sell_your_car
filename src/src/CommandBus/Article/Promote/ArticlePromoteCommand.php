<?php

declare(strict_types=1);

namespace App\CommandBus\Article\Promote;

use App\Entity\Article;
use App\Entity\User;

final class ArticlePromoteCommand
{

    /**
     * ArticlePromoteCommand constructor.
     * @param Article $article
     * @param User $user
     * @param int $period
     */
    public function __construct(
        private Article $article,
        private User $user,
        private int $period
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

}