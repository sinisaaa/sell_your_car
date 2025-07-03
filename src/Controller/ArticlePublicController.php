<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Article;
use App\Entity\Search;
use App\Entity\User;
use App\Event\ArticlesSearchedEvent;
use App\Helper\ArticleHelper;
use App\Helper\CustomQuery;
use App\Helper\CustomQuery\JoinClause;
use App\Helper\CustomQuery\OrderByClause;
use App\Helper\CustomQuery\WhereClauseDQLFilter;
use App\Helper\CustomQuery\WhereClauseFilter;
use App\Service\QueryFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use JsonException;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ArticlePublicController extends BaseController
{

    /**
     * ArticlePublicController constructor.
     * @param EntityManagerInterface $em
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     * @param EventBus $eventBus
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EntityManagerInterface $em,
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator,
        private EventBus $eventBus,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @SWG\Get(
     *     path="/api/public/articles/{article}",
     *     summary="Get article details",
     *     description="Get articles",
     *     tags={"Articles Public"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Article details",
     *         @Model(type=Article::class, groups={"article.get", "article_category_field.get", "article_category_fields.get", "article_category_field_options.get", "location.get", "article_image.get", "article_manufacturer.get", "article_manufacturer_model.get", "article_user.get", "user.rel", "user_location.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 404,
     *          description="Article not found"
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="error"
     *        )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get","article_category_field.get", "article_category_fields.get", "article_category_field_options.get", "location.get", "article_image.get", "article_manufacturer.get", "article_manufacturer_model.get", "article_user.get", "user_roles.get", "user.rel", "user_location.get"})
     * @Route("/api/public/articles/{article}", requirements={"article": "\d+"}, methods={"GET"})
     *
     * @param Article $article
     * @return ApiView
     */
    public function getAction(Article $article): ApiView
    {
        if ($article->getIsDraft()) {
            throw new NotFoundHttpException($this->translator->trans('Exception.Article.Not.Found'));
        }

        $article->incrementHitsCounter();

        if ($this->getNullableUser() instanceof User) {
            ArticleHelper::setFavoriteStatus($article, $this->getNullableUser());
        }

        $this->em->persist($article);
        $this->em->flush();

        return ApiView::create($article);
    }

    /**
     * @SWG\Get(
     *   path="/api/public/articles",
     *   summary="List of all articles",
     *   tags={"Articles Public"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all articles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
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
     *      name="fieldsFilter",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter by custom fields",
     *      @SWG\Schema(
     *         @SWG\Property(property="fieldsFilter", type="string")
     *      )
     *   ),
     *   @SWG\Parameter(
     *      name="globalFilter",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Global filter by title - shuffled keys",
     *      @SWG\Schema(
     *         @SWG\Property(property="globalFilter", type="string")
     *      )
     *   ),
     *  @SWG\Parameter(
     *      name="saveSearch",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Save search in database - use 1 as value",
     *      @SWG\Schema(
     *         @SWG\Property(property="saveSearch", type="number")
     *      )
     *   ),
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/public/articles", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws JsonException
     */
    public function getAllAction(Request $request): array
    {
        $globalFilter = $this->parseGlobalFilter($request->query->get('globalFilter'));
        $globalFilterDQLQuery = $globalFilter['globalFilterDQLQuery'];
        $globalFilterDQLQueryParams = $globalFilter['globalFilterDQLQueryParams'];

        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addLeftJoin(new JoinClause('a.categoryFields', 'aaf'));
        $customQuery->addLeftJoin(new JoinClause('aaf.fieldOptions', 'aafo'));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));

        if (null !== $globalFilterDQLQuery) {
            $customQuery->addAndWhereDQL(new CustomQuery\WhereClauseDQLFilter($globalFilterDQLQuery, $globalFilterDQLQueryParams));
        }

        $customQuery->addOrderBy(new OrderByClause('featured', OrderByClause::DESC));
        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();

        if ($this->getNullableUser() instanceof User) {
            /** @var Article $item */
            foreach ($items as $item) {
                ArticleHelper::setFavoriteStatus($item[0], $this->getNullableUser());
            }
        }

        if (null !== $this->getNullableUser() && 1 === (int)$request->query->get('saveSearch')) {
            $this->eventBus->handle(new ArticlesSearchedEvent($this->getNullableUser(), $request->getRequestUri(), $request->query->all()));
        }

        return $this->getPaginatedItems($pagination, $items);
    }

    /**
     * @SWG\Get(
     *   path="/api/public/articles/last-day",
     *   summary="List of all articles added in last 24 hours",
     *   tags={"Articles Public"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of all articles added in last 24 hours",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
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
     *   ),
     *  @SWG\Parameter(
     *      name="fieldsFilter",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter by custom fields",
     *      @SWG\Schema(
     *         @SWG\Property(property="fieldsFilter", type="string")
     *      )
     *   ),
     *  @SWG\Parameter(
     *      name="saveSearch",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Save search in database - use 1 as value",
     *      @SWG\Schema(
     *         @SWG\Property(property="saveSearch", type="number")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/public/articles/last-day", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws JsonException
     */
    public function getLastDayArticlesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addLeftJoin(new JoinClause('a.categoryFields', 'aaf'));
        $customQuery->addLeftJoin(new JoinClause('aaf.fieldOptions', 'aafo'));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.createdAt', (new \DateTime())->modify('-24 hours'), WhereClauseFilter::OPERAND_GREATER_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();

        if ($this->getNullableUser() instanceof User) {
            /** @var Article $item */
            foreach ($items as $item) {
                ArticleHelper::setFavoriteStatus($item[0], $this->getNullableUser());
            }
        }

        if (null !== $this->getNullableUser() && 1 === (int)$request->query->get('saveSearch')) {
            $this->eventBus->handle(new ArticlesSearchedEvent($this->getNullableUser(), $request->getRequestUri(), $request->query->all()));
        }

        return $this->getPaginatedItems($pagination, $items);
    }

    /**
     * @SWG\Get(
     *   path="/api/public/articles/featured",
     *   summary="List of all featured articles",
     *   tags={"Articles Public"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of all featured articles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Article::class, groups={"article.get", "location.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
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
     *   ),
     *   @SWG\Parameter(
     *      name="fieldsFilter",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Filter by custom fields",
     *      @SWG\Schema(
     *         @SWG\Property(property="fieldsFilter", type="string")
     *      )
     *   ),
     *  @SWG\Parameter(
     *      name="saveSearch",
     *      in="query",
     *      required=false,
     *      type="number",
     *      description="Save search in database - use 1 as value",
     *      @SWG\Schema(
     *         @SWG\Property(property="saveSearch", type="number")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"article.get", "location.get"})
     * @Route("/api/public/articles/featured", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws JsonException
     */
    public function getFeaturedArticlesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->setSelect(new CustomQuery\SelectClause(['a',
            'CASE WHEN (a.featuredFrom <= CURRENT_TIMESTAMP() AND a.featuredTo >= CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END AS featured']));

        $customQuery->addLeftJoin(new JoinClause('a.categoryFields', 'aaf'));
        $customQuery->addLeftJoin(new JoinClause('aaf.fieldOptions', 'aafo'));

        $customQuery->addAndWhere(new WhereClauseFilter('a.status', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.soldAt', null, WhereClauseFilter::OPERAND_IS_NULL));
        $customQuery->addAndWhere(new WhereClauseFilter('a.isDraft', 0, WhereClauseFilter::OPERAND_EQUALS));

        $customQuery->addAndWhere(new WhereClauseFilter('a.featuredFrom', new \DateTime(), WhereClauseFilter::OPERAND_LESS_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('a.featuredTo', new \DateTime(), WhereClauseFilter::OPERAND_GREATER_EQUALS));
        $customQuery->addOrderBy(new OrderByClause('a.createdAt', OrderByClause::DESC));

        $query = $this->queryFromRequest->generate($request, 'a', Article::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        $items = $pagination->getItems();

        if ($this->getNullableUser() instanceof User) {
            /** @var Article $item */
            foreach ($items as $item) {
                ArticleHelper::setFavoriteStatus($item[0], $this->getNullableUser());
            }
        }

        if (null !== $this->getNullableUser() && 1 === (int)$request->query->get('saveSearch')) {
            $this->eventBus->handle(new ArticlesSearchedEvent($this->getNullableUser(), $request->getRequestUri(), $request->query->all()));
        }

        return $this->getPaginatedItems($pagination);
    }

}