<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class RoleController extends BaseController
{

    /**
     * RoleController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/roles/autocomplete",
     *   summary="List roles with autocomplete",
     *   tags={"Roles"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of roles",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Role::class, groups={"role.get"})),
     *      )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   ),
     *   @SWG\Parameter(
     *      name="q",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="Query params, search by name or code",
     *      @SWG\Schema(
     *         @SWG\Property(property="q", type="string")
     *      )
     *   ),
     * )
     *
     * @ViewAnnotation(serializerGroups={"role.get"})
     * @Route("/api/roles/autocomplete", methods={"GET"})
     *
     * @param Request $request
     * @return ApiView
     */
    public function getAutoCompleteAction(Request $request): ApiView
    {
        return ApiView::create(['items' => $this->em->getRepository(Role::class)->findForAutoComplete($request->query->get('q', ''))]);
    }

}