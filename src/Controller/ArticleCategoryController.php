<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use App\Entity\ArticleCategory;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class ArticleCategoryController extends BaseController
{

    /**
     * ArticleCategoryController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/public/article-categories",
     *   summary="List of all article categories with fields",
     *   tags={"Article Category"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of all article categories with fields",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=ArticleCategory::class, groups={"article_category.get", "article_category_fields.get", "article_category_field_options.get", "article_manufacturer.get", "article_manufacturer_model.get", "article_manufacturer_models.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="error"
     *   ),
     * )
     *
     * @ViewAnnotation(serializerGroups={"article_category.get", "article_category_fields.get", "article_category_field_options.get", "article_manufacturer.get", "article_manufacturer_model.get", "article_manufacturer_models.get"})
     * @Route("/api/public/article-categories", methods={"GET"})
     *
     * @return ApiView
     */
    public function getLastDayArticlesAction(): ApiView
    {
        return ApiView::create([
            'items' => $this->em->getRepository(ArticleCategory::class)->findAll()
        ]);
    }

}