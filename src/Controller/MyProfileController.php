<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Form\Type\MyProfileUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\View\View as ApiView;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class MyProfileController extends BaseController
{

    /**
     * MyProfileController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
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
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})),
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
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
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

}