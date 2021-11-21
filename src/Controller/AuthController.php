<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\RecordedEvent;
use App\Entity\User;
use App\Entity\UserToken;
use App\Event\UserRegisteredEvent;
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
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;

final class AuthController extends BaseController
{

    /**
     * AuthController constructor.
     * @param EncodeService $encodeService
     * @param JWTTokenManagerInterface $tokenManager
     * @param EntityManagerInterface $em
     * @param EventBus $eventBus
     * @param RecordedEventService $eventRecorderService
     */
    public function __construct(
        private EncodeService $encodeService,
        private JWTTokenManagerInterface $tokenManager,
        private EntityManagerInterface $em,
        private EventBus $eventBus,
        private RecordedEventService $eventRecorderService
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
            throw new AccessDeniedHttpException('Invalid credentials');
        }

        if (false === $this->encodeService->isPasswordValid($user,$data['password'])) {
            throw new AccessDeniedHttpException('Invalid credentials');
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
     *          response = 401,
     *          description="Account has no permissions"
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
            throw new ConflictHttpException('You already registered user, please wait some time and try again!');
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
            throw new ConflictHttpException('User with same email already exists.');
        }

        $user->setCreatedOn(new DateTime());

        $this->em->persist($user);
        $this->em->flush();

        $this->eventBus->handle(new UserRegisteredEvent());

        return ApiView::create($user);
    }

}