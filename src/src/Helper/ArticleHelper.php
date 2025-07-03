<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\UserFavoriteArticles;

final class ArticleHelper
{

    /**
     * @param Article $article
     * @param User $user
     */
    public static function setFavoriteStatus(Article $article, User $user): void
    {
        /** @var UserFavoriteArticles $userFavoriteArticle */
        foreach($user->getUserFavoriteArticles()->toArray() as $userFavoriteArticle) {
            if ($userFavoriteArticle->getArticle()->getId() === $article->getId()) {
                $article->setFavorite(true);
                return;
            }
        }
    }

}