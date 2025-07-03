<?php

declare(strict_types=1);

namespace App\CommandBus\Article\Create;

use App\Entity\Article;
use App\Entity\ArticleArticleCategoryField;
use App\Entity\ArticleCategoryField;
use App\Entity\ArticleCategoryFieldOption;
use App\Entity\ArticleImage;
use App\Helper\UploadedFileHelper;
use App\Service\ArticleService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ArticleCreateCommandHandler
{

    /**
     * ArticleCreateCommandHandler constructor.
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
     * @param ArticleCreateCommand $command
     */
    public function handle(ArticleCreateCommand $command): void
    {
        $article = $command->getArticle();
        $images = $command->getImages();
        $fields = $command->getFields();

        if (null !== $article->getManufacturerModel() && null === $article->getManufacturer()) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Article.Manufacturer.Not.Selected'));
        }

        if (null !== $article->getManufacturerModel() &&
            false === $article->getManufacturer()->getArticleManufacturerModels()->contains($article->getManufacturerModel()))
        {
            throw new BadRequestHttpException($this->translator->trans('Exception.Article.Manufacturer.Model.Invalid'));
        }

        $article->setCreatedAt(new DateTime());
        $this->em->persist($article);

        $this->articleService->addFields($article, $fields);
        $this->articleService->uploadPhotos($article, $images);
    }

}