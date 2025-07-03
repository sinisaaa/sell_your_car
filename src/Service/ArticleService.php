<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\ArticleArticleCategoryField;
use App\Entity\ArticleCategoryField;
use App\Entity\ArticleCategoryFieldOption;
use App\Entity\ArticleImage;
use App\Helper\UploadedFileHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleService
{

    /**
     * ArticleService constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @param Article $article
     * @param array<mixed> $fields
     */
    public function addFields(Article $article, array $fields): void
    {
        foreach($fields as $fieldId => $fieldValue) {
            /** @var ArticleCategoryField|null $articleField */
            $articleField = $this->em->getRepository(ArticleCategoryField::class)->find((int)$fieldId);

            if (null === $articleField) {
                throw new NotFoundHttpException($this->translator->trans('Exception.Article.Field.Not.Found'));
            }

            if ($article->getCategory()->getId() !== $articleField->getCategory()->getId()) {
                throw new BadRequestHttpException($this->translator->trans('Exception.Article.Field.Not.Belong.To.Category'));
            }

            $articleArticleField = ArticleArticleCategoryField::create($article, $articleField);

            if ($articleField->isOptionsField()) {

                if (false === is_array($fieldValue)) {
                    throw new BadRequestHttpException($this->translator->trans('Exception.Article.Field.Options.Must.Be.Array'));
                }

                foreach ($fieldValue as $optionFieldId) {
                    $fieldOption = $this->em->getRepository(ArticleCategoryFieldOption::class)->find((int)$optionFieldId);

                    if (null === $fieldOption) {
                        throw new NotFoundHttpException($this->translator->trans('Exception.Article.Field.Option.Not.Found'));
                    }

                    if (false === $articleField->getArticleCategoryFieldOptions()->contains($fieldOption)) {
                        throw new ConflictHttpException($this->translator->trans('Exception.Article.Field.Option.Not.Belong'));
                    }

                    $articleArticleField->addFieldOption($fieldOption);
                }
            } else {
                $articleArticleField->setValue($fieldValue);
            }

            $this->em->persist($articleArticleField);
        }
    }

    /**
     * @param Article $article
     * @param array<UploadedFile> $images
     */
    public function uploadPhotos(Article $article, array $images): void
    {
        UploadedFileHelper::validateImages($images);

        foreach ($images as $key => $image) {
            $imageDimension = getimagesize($image->getRealPath());

            $articleImage = new ArticleImage();
            $articleImage->setArticle($article);
            $articleImage->setImageFile($image);
            $articleImage->setWidth($imageDimension[0] ?? null);
            $articleImage->setHeight($imageDimension[1] ?? null);
            $articleImage->setImageOrder($key);
            $articleImage->setExtension($image->getClientOriginalExtension());
            $articleImage->setCreatedAt(new DateTime());

            $this->em->persist($articleImage);
        }
    }

}