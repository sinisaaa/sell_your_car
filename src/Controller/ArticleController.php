<?php

declare(strict_types=1);

namespace App\Controller;

use App\CommandBus\Article\Create\ArticleCreateCommand;
use App\CommandBus\Article\Promote\ArticlePromoteCommand;
use App\CommandBus\Article\Update\ArticleUpdateCommand;
use App\Controller\Base\BaseController;
use App\Entity\UserFavoriteArticles;
use App\Form\Model\ArticlePromoteToFeaturedModel;
use App\Form\Type\ArticleCreateType;
use App\Form\Type\ArticlePromoteToFeaturedType;
use App\Helper\ArticleHelper;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ArticleController extends BaseController
{

    /**
     * ArticleController constructor.
     * @param CommandBus $commandBus
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private CommandBus $commandBus,
        private EntityManagerInterface $em,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/articles",
     *     summary="Creates article",
     *     description="Creates articles",
     *     tags={"Articles"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Created article",
     *     @Model(type=Article::class, groups={"article.get", "article_category_fields.get", "article_category_field.get", "article_category_field_options.get", "article_category_ref", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"}),
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
     *     ),
     *     @SWG\Parameter(
     *         description="Images to upload",
     *         in="body",
     *         name="images",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         @SWG\Schema(
     *              type="array",
     *              @SWG\Items(type="file"),
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="fields",
     *          description="Fields with values [fieldId => fieldValue] for dropdown and checkbox fields [fieldId => [fieldOptionId, fieldOptionId2]]",
     *          type="array",
     *          in="body",
     *          required=true,
     *          required=false,
     *     @SWG\Schema(
     *              type="array",
     *              @SWG\Items(type="integer"),
     *              example={"1 => 2020", "2 => 55KW", "3 => [1, 2]"}
     *          )
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=ArticleCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "article_category_field.get", "article_category_fields.get", "article_category_field_options.get", "article_category_ref", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"})
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

        $images = $request->files->get('images', []);
        $fields = (array)$request->request->get('fields', []);

        /** @var Article $article */
        $article = $form->getData();
        $article->setUser($this->getUser());

        $this->commandBus->handle(new ArticleCreateCommand($article, $images, $fields));

        $this->em->refresh($article);

        return ApiView::create($article);
    }

    /**
     * @SWG\Post(
     *     path="/api/articles/{article}",
     *     summary="Updates article",
     *     description="Updates articles",
     *     tags={"Articles"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated article",
     *     @Model(type=Article::class, groups={"article.get", "article_category_fields.get", "article_category_field.get", "article_category_field_options.get", "article_category_ref", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"}),
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
     *     ),
     *     @SWG\Parameter(
     *         description="Images to upload",
     *         in="body",
     *         name="images",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         @SWG\Schema(
     *              type="array",
     *              @SWG\Items(type="file"),
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="fields",
     *          description="Fields with values [fieldId => fieldValue] for dropdown and checkbox fields [fieldId => [fieldOptionId, fieldOptionId2]]",
     *          type="array",
     *          in="body",
     *          required=true,
     *          required=false,
     *     @SWG\Schema(
     *              type="array",
     *              @SWG\Items(type="integer"),
     *              example={"1 => 2020", "2 => 55KW", "3 => [1, 2]"}
     *          )
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=ArticleCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "article_category_field.get", "article_category_fields.get", "article_category_field_options.get", "article_category_ref", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"})
     * @Route("/api/articles/{article}", methods={"POST"})
     * @Security("is_granted('CAN_MANAGE_ARTICLE', article)")
     *
     * @param Request $request
     * @param Article $article
     *
     * @return ApiView
     */
    public function updateAction(Article $article, Request $request): ApiView
    {
        $form = $this->createForm(ArticleCreateType::class, $article);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        $images = $request->files->get('images', []);
        $fields = (array)$request->request->get('fields', []);

        /** @var Article $article */
        $article = $form->getData();

        $this->commandBus->handle(new ArticleUpdateCommand($article, $images, $fields));

        ArticleHelper::setFavoriteStatus($article, $this->getUser());
        $this->em->refresh($article);

        return ApiView::create($article);
    }

    /**
     * @SWG\Post(
     *     path="/api/articles/{article}/deactivate",
     *     summary="Deactivates article",
     *     description="Deactivates articles",
     *     tags={"Articles"},
     *     @SWG\Response(
     *         response= 204,
     *         description="Empty response"
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Forbbiden user without access"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(statusCode=204)
     * @Route("/api/articles/{article}/deactivate", methods={"POST"})
     * @Security("is_granted('CAN_MANAGE_ARTICLE', article)")
     *
     * @param Article $article
     * @return ApiView
     */
    public function deactivateAction(Article $article): ApiView
    {
        $article->setStatus(false);

        $this->em->persist($article);
        $this->em->flush();

        return ApiView::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @SWG\Post(
     *     path="/api/articles/{article}/sell",
     *     summary="Sell article",
     *     description="Sell articles",
     *     tags={"Articles"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Sold article",
     *         @Model(type=Article::class, groups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Forbbiden user without access"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"})
     * @Route("/api/articles/{article}/sell", methods={"POST"})
     * @Security("is_granted('CAN_MANAGE_ARTICLE', article)")
     *
     * @param Article $article
     * @return ApiView
     */
    public function sellAction(Article $article): ApiView
    {
        if (false === $article->getStatus()) {
            throw new ConflictHttpException($this->translator->trans('Exception.Article.Article.Is.Deactivated'));
        }

        $article->setSoldAt(new \DateTime());

        $this->em->persist($article);
        $this->em->flush();

        ArticleHelper::setFavoriteStatus($article, $this->getUser());

        return ApiView::create($article);
    }

    /**
     * @SWG\Post(
     *     path="/api/articles/{article}/toggle-favorite",
     *     summary="Add or remove article from favorites",
     *     description="Add or remove article from favorites",
     *     tags={"Articles"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Article with updated favorite status",
     *         @Model(type=Article::class, groups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"})
     * @Route("/api/articles/{article}/toggle-favorite", methods={"POST"})
     *
     * @param Article $article
     * @return ApiView
     */
    public function toggleFavoriteAction(Article $article): ApiView
    {
        $user = $this->getUser();
        $favoriteArticle = $this->em->getRepository(UserFavoriteArticles::class)->findOneBy(
            ['article' => $article, 'user' => $user]
        );

        if (null === $favoriteArticle) {
            $favoriteArticle = new UserFavoriteArticles();
            $favoriteArticle->setArticle($article)
                ->setUser($user);
            $article->setFavorite(true);

            $this->em->persist($favoriteArticle);
        } else {
            $this->em->remove($favoriteArticle);
            $article->setFavorite(false);
        }

        $this->em->flush();

        return ApiView::create($article);
    }

    /**
     * @SWG\Post(
     *     path="/api/articles/{article}/promote",
     *     summary="Promotes article to featured for selected period",
     *     description="Promotes article to featured for selected period",
     *     tags={"Articles"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Featured article",
     *         @Model(type=Article::class, groups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="User has no access to article"
     *     ),
     *     @SWG\Response(
     *          response = 409,
     *          description="Not enough credits"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=ArticlePromoteToFeaturedType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"})
     * @Route("/api/articles/{article}/promote", methods={"POST"})
     * @Security("is_granted('CAN_MANAGE_ARTICLE', article)")
     *
     * @param Request $request
     * @param Article $article
     * @return ApiView
     */
    public function promoteAction(Request $request, Article $article): ApiView
    {
        $form = $this->createForm(ArticlePromoteToFeaturedType::class);
        $form->submit($request->request->all());
        $user = $this->getUser();

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var ArticlePromoteToFeaturedModel $featuredData */
        $featuredData = $form->getData();

        $this->commandBus->handle(new ArticlePromoteCommand($article, $user, (int)$featuredData->getNumberOfDays()));

        return ApiView::create($article);
    }

    /**
     * @SWG\Post(
     *     path="/api/articles/{article}/finish-draft",
     *     summary="Switch article from draft to created article",
     *     description="Switch article from draft to created article",
     *     tags={"Articles"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Created article",
     *         @Model(type=Article::class, groups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 409,
     *          description="Article already completed"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get", "article_manufacturer.get", "article_manufacturer_model.get"})
     * @Route("/api/articles/{article}/finish-draft", methods={"POST"})
     *
     * @param Article $article
     * @return ApiView
     */
    public function finishDraftAction(Article $article): ApiView
    {
        if (false === $article->getIsDraft()) {
            throw new ConflictHttpException($this->translator->trans('Exception.Article.Already.Created'));
        }

        $article->setIsDraft(false);
        $article->setCreatedAt(new \DateTime());

        $this->em->flush();

        return ApiView::create($article);
    }


    /**
     * @SWG\Get(
     *     path="/api/articles/last-draft",
     *     summary="Get last draft article for currently logged user",
     *     description="Get last draft article for currently logged user",
     *     tags={"Articles"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Article with draft status",
     *         @Model(type=Article::class, groups={"article.get", "article_category_field.get", "article_category_fields.get", "article_category_field_options.get", "location.get", "article_image.get", "article_manufacturer.get", "article_manufacturer_model.get", "article_user.get", "user.rel", "user_location.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get","article_category_field.get", "article_category_fields.get", "article_category_field_options.get", "location.get", "article_image.get", "article_manufacturer.get", "article_manufacturer_model.get", "article_user.get", "user.rel", "user_location.get"})
     * @Route("/api/articles/last-draft", methods={"GET"})
     *
     * @return ApiView
     */
    public function getLastDraft(): ApiView
    {
        $lastDraft = $this->em->getRepository(Article::class)->findLastDraftForUser($this->getUser());

        return ApiView::create(0 < count($lastDraft) ? $lastDraft[0] : []);
    }

}