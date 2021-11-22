<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\RecordedEvent;
use App\Entity\User;
use App\Entity\UserEmailConfirmToken;
use App\Entity\UserForgotPasswordToken;
use App\Entity\UserToken;
use App\Event\UserForgotPasswordEvent;
use App\Event\UserRegisteredEvent;
use App\Form\Model\UserChangePasswordModel;
use App\Form\Type\UserChangePasswordType;
use App\Form\Type\UserLoginType;
use App\Form\Type\UserRegisterType;
use App\Service\EncodeService;
use App\Service\RecordedEventService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthController extends BaseController
{

    /**
     * AuthController constructor.
     * @param EncodeService $encodeService
     * @param JWTTokenManagerInterface $tokenManager
     * @param EntityManagerInterface $em
     * @param EventBus $eventBus
     * @param RecordedEventService $eventRecorderService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private EncodeService $encodeService,
        private JWTTokenManagerInterface $tokenManager,
        private EntityManagerInterface $em,
        private EventBus $eventBus,
        private RecordedEventService $eventRecorderService,
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/login",
     *     summary="Web login",
     *     description="Web login, generates auth token",
     *     tags={"Authentication and registration"},
     *     @SWG\Response(
     *          response = 200,
     *          description="Returns auth token",
     *          @SWG\Schema(ref="#/definitions/AccessToken")
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Account has no permissions"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="Incorrect account credentials"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="login",
     *          @Model(type=UserLoginType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user-token.get"})
     * @Route("/api/login", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws \Exception
     */
    public function loginAction(Request $request): ApiView
    {
        $form = $this->createForm(UserLoginType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        $data = $form->getData();

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $data['email'], 'active' => true]);

        if (null === $user) {
            throw new AccessDeniedHttpException($this->translator->trans('Exception.Auth.Invalid.Credentials'));
        }

        if (false === $this->encodeService->isPasswordValid($user,$data['password'])) {
            throw new AccessDeniedHttpException($this->translator->trans('Exception.Auth.Invalid.Credentials'));
        }

        $user->setLastLogin(new \DateTime());

        $generatedToken = $this->tokenManager->create($user);
        $userToken = UserToken::create($generatedToken, $user);

        $this->em->persist($user);
        $this->em->persist($userToken);
        $this->em->flush();

        return ApiView::create($userToken);
    }

    /**
     * @SWG\Post(
     *     path="/api/logout",
     *     summary="Web logout",
     *     description="Logout and delete user tokens",
     *     tags={"Authentication and registration"},
     *     @SWG\Response(
     *          response = 204,
     *          description=""
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Account has no permissions"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     * )
     *
     * @Route("/api/logout", methods={"POST"})
     *
     * @return ApiView
     * @ViewAnnotation(statusCode=204)
     *
     * @throws \Exception
     */
    public function logoutAction(): ApiView
    {
        $user = $this->getUser();

        $userTokens = $this->em->getRepository(UserToken::class)->findBy(['user' => $user]);

        foreach($userTokens as $userToken) {
            $this->em->remove($userToken);
        }

        $this->em->flush();

        return ApiView::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @SWG\Post(
     *     path="/api/register",
     *     summary="Create user",
     *     description="Create user",
     *     tags={"Authentication and registration"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Created user",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *          response = 409,
     *          description="Account with same email already exists"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="register",
     *          @Model(type=UserRegisterType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/register", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function registerAction(Request $request): ApiView
    {
        if ($this->eventRecorderService->hasRecordedEvents(RecordedEvent::REGISTRATION_EVENT)) {
            throw new ConflictHttpException($this->translator->trans('Exception.Auth.Register.User.Wait.Time'));
        }

        if ($this->getParameter('security_question_answer') !== $request->request->get('securityQuestion')) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Auth.Register.Security.Answer.Invalid'));
        }

        $form = $this->createForm(UserRegisterType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var User $user */
        $user = $form->getData();
        $user->setUsername($user->getEmail());

        $existingUser = $this->em->getRepository(User::class)->findOneBy([
            'username' => $user->getUsername()
        ]);

        if (null !== $existingUser) {
            throw new ConflictHttpException($this->translator->trans('Exception.Auth.Register.User.Email.Already.Exist'));
        }

        $user->setCreatedOn(new DateTime());
        $emailToken = UserEmailConfirmToken::create($user);

        $this->em->persist($user);
        $this->em->persist($emailToken);
        $this->em->flush();

        $this->eventBus->handle(new UserRegisteredEvent($user->getEmail(), $user->getName(), $emailToken->getToken()));

        return ApiView::create($user);
    }

    /**
     * @SWG\Post(
     *     path="/api/confirm-email",
     *     summary="Confirm user email and set user status to active",
     *     description="Confirm user email and set user status to active",
     *     tags={"Authentication and registration"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Returns updated user",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Validation token not send"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          name="token",
     *          in="body",
     *          required=true,
     *          description="Email validation token",
     *          @SWG\Schema(
     *              @SWG\Property(property="token", type="string")
     *           )
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/confirm-email", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function confirmEmailAction(Request $request): ApiView
    {
        if (null === $tokenCode = $request->request->get('token')) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Auth.Confirm.Email.Token.Not.Sent'));
        }

        /** @var UserEmailConfirmToken|null $emailConfirmToken */
        $emailConfirmToken = $this->em->getRepository(UserEmailConfirmToken::class)->findOneBy(['token' => $tokenCode]);

        if (null === $emailConfirmToken) {
            throw new NotFoundHttpException($this->translator->trans('Exception.Auth.Confirm.Email.Token.Invalid'));
        }

        if ($emailConfirmToken->isTokenExpired()) {
            throw new ConflictHttpException($this->translator->trans('Exception.Auth.Confirm.Email.Token.Expired'));
        }

        $user = $emailConfirmToken->getUser();
        $user->setActive(true);

        $this->em->remove($emailConfirmToken);
        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }

    /**
     * @SWG\Post(
     *     path="/api/forgot-password",
     *     summary="Send forgot password mail",
     *     description="Send forgot password to mail if mail exists in system. Always return 204",
     *     tags={"Authentication and registration"},
     *     @SWG\Response(
     *          response= 204,
     *          description="Mail sent"
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Email address is not provided"
     *     ),
     *     @SWG\Response(
     *          response="default",
     *          description="error"
     *     ),
     *     @SWG\Parameter(
     *          name="email",
     *          in="body",
     *          required=true,
     *          description="Email address",
     *          @SWG\Schema(
     *              @SWG\Property(property="email", type="string")
     *          )
     *     )
     * )
     *
     * @ViewAnnotation(statusCode=204)
     * @Route("/api/forgot-password", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function forgotPasswordAction(Request $request): ApiView
    {
        if (null === $emailAddress = $request->request->get('email')) {
            throw new BadRequestHttpException($this->translator->trans('Exception.Auth.Forgot.Password.Email.Not.Sent'));
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $emailAddress, 'active' => true]);

        if (null !== $user) {
            $forgotPasswordToken = UserForgotPasswordToken::create($user);
            $this->em->persist($forgotPasswordToken);
            $this->em->flush();

            $this->eventBus->handle(new UserForgotPasswordEvent($user, $forgotPasswordToken->getToken()));
        }

        return ApiView::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @SWG\Post(
     *     path="/api/change-password",
     *     summary="Change user password",
     *     description="Change user password",
     *     tags={"Authentication and registration"},
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
     *          response = 404,
     *          description="Reset password token not found"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="register",
     *          @Model(type=UserChangePasswordType::class)
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "user_roles.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/change-password", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws Exception
     */
    public function changePasswordAction(Request $request): ApiView
    {
        $form = $this->createForm(UserChangePasswordType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        /** @var UserChangePasswordModel $data */
        $data = $form->getData();

        /** @var UserForgotPasswordToken|null $resetPasswordToken */
        $resetPasswordToken = $this->em->getRepository(UserForgotPasswordToken::class)->findOneBy(['token' => $data->getToken()]);

        if (null === $resetPasswordToken) {
            throw new NotFoundHttpException($this->translator->trans('Exception.Auth.Reset.Password.Token.Not.Found'));
        }

        if ($resetPasswordToken->isTokenExpired()) {
            throw new ConflictHttpException($this->translator->trans('Exception.Auth.Reset.Password.Token.Expired'));
        }

        $user = $resetPasswordToken->getUser();
        $user->setPassword(null);
        $user->setPlainPassword($data->getPassword());

        $this->em->remove($resetPasswordToken);
        $this->em->persist($user);
        $this->em->flush();

        return ApiView::create($user);
    }


}