<?php

declare(strict_types=1);

namespace App\CommandBus\Article\Update;

use App\Service\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ArticleUpdateCommandHandler
{

    /**
     * ArticleUpdateCommandHandler constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param ArticleService $articleService
     */
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private ArticleService $articleService
    )
    {
    }

    /**
     * @param ArticleUpdateCommand $command
     */
    public function handle(ArticleUpdateCommand $command): void
    {
        $article = $command->getArticle();

        if (null !== $article->getManufacturerModel() && null === $article->getManufacturer()) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Article.Manufacturer.Not.Selected'));
        }

        if (null !== $article->getManufacturerModel() &&
            false === $article->getManufacturer()->getArticleManufacturerModels()->contains($article->getManufacturerModel()))
        {
            throw new BadRequestHttpException($this->translator->trans('Exception.Article.Manufacturer.Model.Invalid'));
        }

        foreach($article->getCategoryFields() as $categoryField) {
            $this->em->remove($categoryField);
        }
        $this->articleService->addFields($article, $command->getFields());

        foreach($article->getArticleImages() as $image) {
            $this->em->remove($image);
        }
        $this->articleService->uploadPhotos($article, $command->getImages());

        $this->em->persist($article);
    }

}