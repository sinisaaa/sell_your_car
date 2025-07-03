<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Base\BaseController;
use App\Entity\Article;
use App\Form\Model\ArticlePromoteToFeaturedModel;
use App\Form\Type\ArticlePromoteToFeaturedType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class AdminArticleController
 *
 * @Route("/api/admin")
 */
final class AdminArticleController extends BaseController
{

    /**
     * AdminArticleController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/admin/articles/{article}/promote",
     *     summary="Promotes article to featured",
     *     description="Promotes article to featured",
     *     tags={"Admin Articles"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Promotes article to featured",
     *     @Model(type=Article::class, groups={"article.get", "location.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Forbidden"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=ArticlePromoteToFeaturedType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/articles/{article}/promote", methods={"POST"})
     *
     * @param Article $article
     * @param Request $request
     * @return ApiView
     */
    public function promoteToFeaturedAction(Article $article, Request $request): ApiView
    {
        $form = $this->createForm(ArticlePromoteToFeaturedType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var ArticlePromoteToFeaturedModel $featuredData */
        $featuredData = $form->getData();

        if (null !== $article->getFeaturedTo() && true === $article->isFeatured()) {
            $oldDateTo = clone $article->getFeaturedTo();
            $article->setFeaturedTo($oldDateTo->modify('+' . $featuredData->getNumberOfDays() . 'days'));
        } else {
            $article->setFeaturedFrom(new DateTime());
            $article->setFeaturedTo((new DateTime())->modify('+' . $featuredData->getNumberOfDays() . 'days'));
        }

        $this->em->persist($article);
        $this->em->flush();

        return ApiView::create($article);
    }

}