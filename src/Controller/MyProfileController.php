<?php

declare(strict_types=1);

namespace App\Controller;

use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class MyProfileController extends BaseController
{

    /**
     * @SWG\Get(
     *   path="/api/my-profile",
     *   summary="Get profile data for current logged in user",
     *   tags={"My Profile"},
     *   @SWG\Response(
     *     response=200,
     *     description="Get profile data for current logged in user",
     *     @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
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
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/my-profile", methods={"GET"})
     *
     * @return ApiView
     */
    public function getProfileAction(): ApiView
    {
        return ApiView::create($this->getUser());
    }

}