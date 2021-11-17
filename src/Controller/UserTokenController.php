<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserToken;
use FOS\RestBundle\View\View as ApiView;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;

final class UserTokenController extends BaseController
{

    /**
     * UserTokenController constructor.
     * @param JWTTokenManagerInterface $tokenManager
     * @param string $jwtRefreshTokenTTL
     */
    public function __construct(private JWTTokenManagerInterface $tokenManager, private string $jwtRefreshTokenTTL)
    {
    }

    /**
     * * @SWG\Post(
     *     path="/api/token/refresh",
     *     summary="Refresh token",
     *     description="Generate new authorization token, based on refresh token",
     *     tags={"Authentication and registration"},
     *     @SWG\Response(
     *          response = 200,
     *          description="Returns auth token",
     *          @SWG\Schema(ref="#/definitions/AccessToken")
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Refresh token empty",
     *     ),
     *     @SWG\Response(
     *          response = 401,
     *          description="Refresh token expired",
     *     ),
     *     @SWG\Response(
     *          response = 404,
     *          description="Refresh token not found",
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          name="refreshToken",
     *          in="body",
     *          required=true,
     *          description="Refresh token",
     *          @SWG\Schema(
     *              @SWG\Property(property="refreshToken", type="string")
     *           )
     *     )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user-token.get"})
     * @Route("/api/token/refresh", methods="POST")
     *
     * @param Request $request
     * @return ApiView
     *
     * @throws \Exception
     */
    public function refreshTokenAction(Request $request): ApiView
    {
        if (!($refreshToken = $request->get('refreshToken'))) {
            throw new BadRequestHttpException('Refresh token not sent!');
        }

        $em = $this->getDoctrine()->getManager();
        $token = $em->getRepository(UserToken::class)->findOneBy(['refreshToken' => $refreshToken]);

        if (null === $token) {
            throw new NotFoundHttpException('Refresh token not found');
        }

        if ($token->isRefreshExpired((int)$this->jwtRefreshTokenTTL)) {
            throw new UnauthorizedHttpException('Refresh token expired');
        }

        $generatedToken = $this->tokenManager->create($token->getUser());
        $newToken = UserToken::create($generatedToken, $token->getUser());

        $em->persist($newToken);
        $em->flush();

        return ApiView::create($newToken);
    }

}