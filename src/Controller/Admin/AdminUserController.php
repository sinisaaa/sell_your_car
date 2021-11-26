<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Base\BaseController;
use App\Entity\Role;
use App\Helper\ValueObjects\RoleCode;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;

final class AdminUserController extends BaseController
{

    /**
     * AdminUserController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
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

}