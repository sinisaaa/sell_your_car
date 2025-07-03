<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Base\BaseController;
use App\Entity\Role;
use App\Form\Model\AdminChangeUserPasswordModel;
use App\Form\Model\UserSetCreditsModel;
use App\Form\Type\AdminChangeUserPasswordType;
use App\Form\Type\AdminUpdateUserType;
use App\Form\Type\UserSetCreditsType;
use App\Helper\ValueObjects\RoleCode;
use App\Service\QueryFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Swagger\Annotations as SWG;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AdminUserController
 * @package App\Controller\Admin
 *
 * @Route("/api/admin")
 */
final class AdminUserController extends BaseController
{

    /**
     * AdminUserController constructor.
     * @param EntityManagerInterface $em
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EntityManagerInterface $em,
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/admin/users/{user}/promote",
     *     summary="Promotes user to car dealer",
     *     description="Promotes user to car dealer",
     *     tags={"Admin Users"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated user",
     *     @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"}),
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
     * @Route("/users/{user}/promote", methods={"POST"})
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
     * @SWG\Post(
     *     path="/api/admin/users/{user}/demote",
     *     summary="Demote user from car dealer to regular",
     *     description="Demote user from car dealer",
     *     tags={"Admin Users"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated user",
     *     @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"}),
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
     * @Route("/users/{user}/demote", methods={"POST"})
     *
     * @param User $user
     * @return ApiView
     *
     */
    public function demoteUserToDealerAction(User $user): ApiView
    {
        /** @var Role $carDealerRole */
        $carDealerRole = $this->em->getRepository(Role::class)->findOneBy(['code' => RoleCode::CAR_DEALER]);
        $user->removeRole($carDealerRole);
        $user->setType(User::TYPE_USER);

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
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_credits.get"})),
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
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_credits.get"})
     * @Route("/users", methods={"GET"})
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

    /**
     * @SWG\Post(
     *     path="/api/admin/users/set-credits",
     *     summary="Sets credits to user",
     *     description="Sets creadits to user",
     *     tags={"Admin Users"},
     *     @SWG\Response(
     *        response= 200,
     *        description="Updated user",
     *        @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_credits.get"}),
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Validation error"
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="User has no access"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="article",
     *          @Model(type=UserSetCreditsType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_credits.get"})
     * @Route("/users/set-credits", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     */
    public function setCreditsAction(Request $request): ApiView
    {
        $form = $this->createForm(UserSetCreditsType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var UserSetCreditsModel $setCreditsModel */
        $setCreditsModel = $form->getData();

        $user = $setCreditsModel->getUser();
        $user->setActiveCredits($setCreditsModel->getActiveCredits());
        $user->setPassiveCredits($setCreditsModel->getPassiveCredits());

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

    /**
     * @SWG\Post(
     *     path="/api/admin/users/{user}/deactivate-toggle",
     *     summary="Deactivates - activates user",
     *     description="Deacrivates - activates user",
     *     tags={"Admin Users"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated user",
     *     @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"}),
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
     * @Route("/users/{user}/deactivate-toggle", methods={"POST"})
     *
     * @param User $user
     * @return ApiView
     *
     */
    public function deactivateToggleAction(User $user): ApiView
    {
        $user->setActive(!$user->getActive());

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

    /**
     * @SWG\Put(
     *     path="/api/admin/users/{user}",
     *     summary="Update user",
     *     description="Update user data",
     *     tags={"Admin Users"},
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
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Account has no permissions"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="user",
     *          @Model(type=AdminUpdateUserType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_notifications.get"})
     * @Route("/users/{user}", methods={"PUT"})
     *
     * @param Request $request
     * @param User $user
     * @return ApiView
     */
    public function updateUserAction(Request $request, User $user): ApiView
    {
        $form = $this->createForm(AdminUpdateUserType::class, $user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var User $user */
        $user = $form->getData();

        /** @var User|null $existingUser */
        $existingUser = $this->em->getRepository(User::class)->findOneBy([
            'email' => $user->getEmail()
        ]);

        if (null !== $existingUser && $user->getId() !== $existingUser->getId()) {
            throw new ConflictHttpException($this->translator->trans('Exception.Auth.Register.User.Email.Already.Exist'));
        }

        $user->setUsername($user->getEmail());

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

    /**
     * @SWG\Post(
     *     path="/api/admin/users/{user}/change-password",
     *     summary="Changes user password",
     *     description="Changes user password",
     *     tags={"Admin Users"},
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
     *          description="Unauthorized"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Account has no permissions"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="password",
     *          @Model(type=AdminChangeUserPasswordType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get", "user_notifications.get"})
     * @Route("/users/{user}/change-password", methods={"POST"})
     *
     * @param Request $request
     * @param User $user
     * @return ApiView
     */
    public function changeUserPasswordAction(Request $request, User $user): ApiView
    {
        $form = $this->createForm(AdminChangeUserPasswordType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var AdminChangeUserPasswordModel $password */
        $password = $form->getData();

        $user->setPassword(null);
        $user->setPlainPassword($password->getPassword());

        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }
}