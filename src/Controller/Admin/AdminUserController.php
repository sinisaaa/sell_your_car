<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Base\BaseController;
use App\Entity\Role;
use App\Helper\ValueObjects\RoleCode;
use App\Service\QueryFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Swagger\Annotations as SWG;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;

final class AdminUserController extends BaseController
{

    /**
     * AdminUserController constructor.
     * @param EntityManagerInterface $em
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        private EntityManagerInterface $em,
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator
    )
    {
    }

    /**
     * @SWG\Put(
     *     path="/api/admin/users/{user}/promote",
     *     summary="Promotes user to car dealer",
     *     description="Promotes user to car dealer",
     *     tags={"Admin Users"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated user",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Account not logged in"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Forbidden"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/admin/users/{user}/promote", methods={"PUT"})
     *
     * @param User $user
     * @return ApiView
     *
     */
    public function promoteUserToDealerAction(User $user): ApiView
    {
        /** @var Role $carDealerRole */
        $carDealerRole = $this->em->getRepository(Role::class)->findOneBy(['code' => RoleCode::CAR_DEALER]);
        $user->addRole($carDealerRole);
        $user->setType(User::TYPE_CAR_DEALER);

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

    /**
     * @SWG\Get(
     *   path="/api/admin/users",
     *   summary="List of all users",
     *   tags={"Admin Users"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all users",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})),
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
     *      description="Sort query, alias is u (ex: sort=u.name)",
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
     *      description="Filter query, alias is u",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/admin/users", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws \JsonException
     */
    public function getAllAction(Request $request): array
    {
        $query = $this->queryFromRequest->generate($request, 'u', User::class);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        return $this->getPaginatedItems($pagination);
    }

}