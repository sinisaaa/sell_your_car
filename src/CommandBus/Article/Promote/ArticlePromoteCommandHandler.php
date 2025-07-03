<?php

declare(strict_types=1);

namespace App\CommandBus\Article\Promote;

use App\Entity\Article;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ArticlePromoteCommandHandler
{

    /**
     * ArticlePromoteCommandHandler constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(private EntityManagerInterface $em, private TranslatorInterface $translator)
    {
    }

    /**
     * @param ArticlePromoteCommand $command
     */
    public function handle(ArticlePromoteCommand $command): void
    {
        $user = $command->getUser();
        $article = $command->getArticle();
        $price = Article::getPeriodFeaturedPrice($command->getPeriod());

        if ($price > $user->getActiveCredits()) {
            throw new ConflictHttpException($this->translator->trans('Exception.Article.Promote.Not.Enough.Credits'));
        }

        $user->setActiveCredits($user->getActiveCredits() - $price);

        if (null !== $article->getFeaturedTo() && true === $article->isFeatured()) {
            $oldDateTo = clone $article->getFeaturedTo();
            $article->setFeaturedTo($oldDateTo->modify('+' . $command->getPeriod() . 'days'));
        } else {
            $article->setFeaturedFrom(new DateTime());
            $article->setFeaturedTo((new DateTime())->modify('+' . $command->getPeriod() . 'days'));
        }

        $this->em->persist($article);
        $this->em->persist($user);
    }


}