<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\User;
use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class PublicUserController
 * @package App\Controller
 *
 * @Route("/api/public")
 */
final class UserPublicController extends BaseController
{

    /**
     * @SWG\Get(
     *   path="/api/public/users/{user}",
     *   summary="Get profile data for user",
     *   tags={"Public users"},
     *   @SWG\Response(
     *     response=200,
     *     description="Get profile data for user",
     *     @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="User not found"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   )
     * )
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/users/{user}", methods={"GET"})
     *
     * @param User $user
     * @return ApiView
     */
    public function getUserDetailAction(User $user): ApiView
    {
        return ApiView::create($user);
    }

}