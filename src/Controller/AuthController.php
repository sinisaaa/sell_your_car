<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserToken;
use App\Form\Type\UserLoginType;
use App\Service\EncodeService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
     */
    public function __construct(private EncodeService $encodeService, private JWTTokenManagerInterface $tokenManager)
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
    public function login(Request $request): ApiView
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

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($userToken);
        $em->flush();

        return ApiView::create($userToken);
    }

}