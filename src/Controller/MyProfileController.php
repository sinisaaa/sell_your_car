<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Article;
use App\Form\Model\MyProfileChangePasswordModel;
use App\Form\Type\MyProfileChangePasswordType;
use App\Form\Type\MyProfileUpdateType;
use App\Helper\ArticleHelper;
use App\Helper\CustomQuery;
use App\Helper\CustomQuery\JoinClause;
use App\Helper\CustomQuery\OrderByClause;
use App\Helper\CustomQuery\WhereClauseFilter;
use App\Service\EncodeService;
use App\Service\QueryFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\View\View as ApiView;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MyProfileController extends BaseController
{

    /**
     * MyProfileController constructor.
     * @param EntityManagerInterface $em
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     * @param EncodeService $encodeService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EntityManagerInterface $em,
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator,
        private EncodeService $encodeService,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/my-profile",
     *   summary="Get profile data for current logged in user",
     *   tags={"My Profile"},
     *   @SWG\Response(
     *     response=200,
     *     description="Get profile data for current logged in user",
     *     @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_credits.get", "user_notifications.get"})
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        )
     * )
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_credits.get", "user_notifications.get"})
     * @Route("/api/my-profile", methods={"GET"})
     *
     * @return ApiView
     */
    public function getProfileAction(): ApiView
    {
        return ApiView::create($this->getUser());
    }

    /**
     * @SWG\Put(
     *     path="/api/my-profile",
     *     summary="Update current user",
     *     description="Update current user data",
     *     tags={"My Profile"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated user",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_notifications.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Account has no permissions"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="register",
     *          @Model(type=MyProfileUpdateType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_notifications.get"})
     * @Route("/api/my-profile", methods={"PUT"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function updateAction(Request $request): ApiView
    {
        $user = $this->getUser();

        $form = $this->createForm(MyProfileUpdateType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var User $user */
        $user = $form->getData();

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

    /**
     * @SWG\Get(
     *   path="/api/my-profile/my-articles",
     *   summary="List of currently logged user articles",
     *   tags={"My Profile"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of currently logged user articles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *       response = 401,
     *       description="Account has no permissions"
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="error"
     *   ),
     *   @SWG\Parameter(
     *      name="perPage",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Number of items per page",
     *      @SWG\Schema(
     *         @SWG\Property(property="perPage", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Currently selected page",
     *      @SWG\Schema(
     *         @SWG\Property(property="page", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="sort",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort query, alias is a (ex: sort=a.title)",
     *      @SWG\Schema(
     *         @SWG\Property(property="sort", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="direction",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort direction",
     *      @SWG\Schema(
     *         @SWG\Property(property="direction", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="filters",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter query, alias is a",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/my-profile/my-articles", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     */
    public function getMyArticlesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.user', $this->getUser(), WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));

        $customQuery->addOrderBy(new OrderByClause('featured', OrderByClause::DESC));
        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();

        /** @var Article $item */
        foreach ($items as $item) {
            ArticleHelper::setFavoriteStatus($item[0], $this->getUser());
        }

        return $this->getPaginatedItems($pagination, $items);
    }

    /**
     * @SWG\Get(
     *   path="/api/my-profile/my-sold-articles",
     *   summary="List of currently logged user sold articles",
     *   tags={"My Profile"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of currently logged user sold articles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *       response = 401,
     *       description="Account has no permissions"
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="error"
     *   ),
     *   @SWG\Parameter(
     *      name="perPage",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Number of items per page",
     *      @SWG\Schema(
     *         @SWG\Property(property="perPage", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Currently selected page",
     *      @SWG\Schema(
     *         @SWG\Property(property="page", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="sort",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort query, alias is a (ex: sort=a.title)",
     *      @SWG\Schema(
     *         @SWG\Property(property="sort", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="direction",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort direction",
     *      @SWG\Schema(
     *         @SWG\Property(property="direction", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="filters",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter query, alias is a",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/my-profile/my-sold-articles", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     */
    public function getMySoldArticlesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NOT_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.user', $this->getUser(), WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));

        $customQuery->addOrderBy(new OrderByClause('featured', OrderByClause::DESC));
        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();

        /** @var Article $item */
        foreach ($items as $item) {
            ArticleHelper::setFavoriteStatus($item[0], $this->getUser());
        }

        return $this->getPaginatedItems($pagination, $items);
    }


    /**
     * @SWG\Get(
     *   path="/api/my-profile/my-featured-articles",
     *   summary="List of currently logged user featured articles",
     *   tags={"My Profile"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of currently logged user featured articles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *       response = 401,
     *       description="Account has no permissions"
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="error"
     *   ),
     *   @SWG\Parameter(
     *      name="perPage",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Number of items per page",
     *      @SWG\Schema(
     *         @SWG\Property(property="perPage", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Currently selected page",
     *      @SWG\Schema(
     *         @SWG\Property(property="page", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="sort",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort query, alias is a (ex: sort=a.title)",
     *      @SWG\Schema(
     *         @SWG\Property(property="sort", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="direction",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort direction",
     *      @SWG\Schema(
     *         @SWG\Property(property="direction", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="filters",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter query, alias is a",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/my-profile/my-featured-articles", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     */
    public function getMyFeaturedArticlesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.user', $this->getUser(), WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.featuredFrom', new \DateTime(), WhereClauseFilter::OPERAND_LESS_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.featuredTo', new \DateTime(), WhereClauseFilter::OPERAND_GREATER_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));

        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();

        /** @var Article $item */
        foreach ($items as $item) {
            ArticleHelper::setFavoriteStatus($item[0], $this->getUser());
        }

        return $this->getPaginatedItems($pagination, $items);
    }

    /**
     * @SWG\Get(
     *   path="/api/my-profile/my-favorite-articles",
     *   summary="List of currently logged user favorite articles",
     *   tags={"My Profile"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of currently logged user favorite articles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *       response = 401,
     *       description="Account has no permissions"
     *   ),
     *   @SWG\Response(
     *      response="default",
     *      description="error"
     *   ),
     *   @SWG\Parameter(
     *      name="perPage",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Number of items per page",
     *      @SWG\Schema(
     *         @SWG\Property(property="perPage", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Currently selected page",
     *      @SWG\Schema(
     *         @SWG\Property(property="page", type="number")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="sort",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort query, alias is a (ex: sort=a.title)",
     *      @SWG\Schema(
     *         @SWG\Property(property="sort", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="direction",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Sort direction",
     *      @SWG\Schema(
     *         @SWG\Property(property="direction", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="filters",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter query, alias is a",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/my-profile/my-favorite-articles", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     */
    public function getMyFavoriteArticlesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));

        $customQuery->addJoin(new JoinClause('a.favoriteByUsers', 'fu'));
        $customQuery->addAndWhere(new WhereClauseFilter('fu.user', $this->getUser(), WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();
        /** @var Article $item */
        foreach ($items as $item) {
            $item[0]->setFavorite(true);
        }

        return $this->getPaginatedItems($pagination, $items);
    }

    /**
     * @SWG\Post(
     *     path="/api/my-profile/change-password",
     *     summary="Change current user password",
     *     description="Change current user password",
     *     tags={"My Profile"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated user",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="register",
     *          @Model(type=MyProfileChangePasswordType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/my-profile/change-password", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function changePasswordAction(Request $request): ApiView
    {
        $form = $this->createForm(MyProfileChangePasswordType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var MyProfileChangePasswordModel $data */
        $data = $form->getData();

        $user = $this->getUser();

        if (false === $this->encodeService->isPasswordValid($user, $data->getOldPassword())) {
            throw new ConflictHttpException($this->translator->trans('Exception.My.Profile.Invalid.Old.Password'));
        }

        $user->setPassword(null);
        $user->setPlainPassword($data->getPassword());

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

}