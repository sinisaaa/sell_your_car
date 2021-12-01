<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Form\Type\ArticleCreateType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class ArticleController extends BaseController
{

    /**
     * ArticleController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/articles",
     *     summary="Creates article",
     *     description="Creates articles",
     *     tags={"Article"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Created article",
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
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=ArticleCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/articles", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     */
    public function createAction(Request $request): ApiView
    {
        $form = $this->createForm(ArticleCreateType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var Article $article */
        $article = $form->getData();
        $article->setUser($this->getUser());
        $article->setCreatedAt(new DateTime());

        $this->em->persist($article);
        $this->em->flush();

        return ApiView::create($article);
    }

}