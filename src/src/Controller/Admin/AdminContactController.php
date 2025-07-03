<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\CommandBus\Contact\Remove\ContactRemoveCommand;
use App\Controller\Base\BaseController;
use App\Entity\Contact;
use App\Service\QueryFromRequest;
use FOS\RestBundle\View\View as ApiView;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;

/**
 * Class AdminContactController
 * @package App\Controller\Admin
 *
 * @Route("/api/admin")
 */
final class AdminContactController extends BaseController
{

    /**
     * AdminContactController constructor.
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     * @param CommandBus $commandBus
     */
    public function __construct(
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator,
        private CommandBus $commandBus
    )
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/admin/contacts",
     *   summary="List of contact messages",
     *   tags={"Contacts"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all contact messages",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Contact::class, groups={"contact.get"})),
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
     *      description="Sort query, alias is c (ex: sort=c.firstName)",
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
     *      description="Filter query, alias is c",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"contact.get"})
     * @Route("/contacts", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws \JsonException
     */
    public function getAllAction(Request $request): array
    {
        $query = $this->queryFromRequest->generate($request, 'c', Contact::class);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        return $this->getPaginatedItems($pagination);
    }

    /**
     * @SWG\Delete(
     *   path="/api/admin/contacts",
     *   summary="Mass remove contact messages",
     *   tags={"Contacts"},
     *   @SWG\Response(
     *     response=204,
     *     description="Contacts removed",
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Contact not found"
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Unauthorized"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   ),
     *   @SWG\Parameter(
     *     name="ids",
     *     description="Every element in array represent contact id",
     *     type="array",
     *     in="body",
     *     @SWG\Items(type="integer"),
     *     collectionFormat="multi",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(type="integer"),
     *        example= {1,2,3}
     *       )
     *    )
     * )
     * @ViewAnnotation(statusCode=204)
     * @Route("/contacts", methods={"DELETE"})
     *
     * @param Request $request
     * @return ApiView
     */
    public function removeContactMessageAction(Request $request): ApiView
    {
        /** @var int[] $ids */
        $ids = $request->request->get('ids', []);

        $this->commandBus->handle(new ContactRemoveCommand($ids));

        return ApiView::create([], Response::HTTP_NO_CONTENT);
    }

}