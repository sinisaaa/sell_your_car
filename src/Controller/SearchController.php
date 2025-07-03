<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Search;
use App\Helper\CustomQuery;
use App\Helper\CustomQuery\WhereClauseFilter;
use App\Service\QueryFromRequest;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class SearchController extends BaseController
{

    /**
     * SearchController constructor.
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator
    )
    {
    }


    /**
     * @SWG\Get(
     *   path="/api/searches",
     *   summary="List of user saved searches",
     *   tags={"Searches"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all user saved searches with pagination",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Search::class, groups={"search.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Forbidden"
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
     *      description="Sort query, alias is s (ex: sort=s.url)",
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
     *      description="Filter query, alias is s",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"search.get"})
     * @Route("/api/searches", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws \JsonException
     */
    public function getAllSavedSearchesAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->addAndWhere(new WhereClauseFilter('s.user', $this->getUser(), WhereClauseFilter::OPERAND_EQUALS));
        $query = $this->queryFromRequest->generate($request, 's', Search::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        return $this->getPaginatedItems($pagination);
    }

}