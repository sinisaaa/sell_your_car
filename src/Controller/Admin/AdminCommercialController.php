<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Base\BaseController;
use App\Entity\Commercial;
use App\Form\Type\CommercialCreateType;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View as ApiView;

/**
 * Class AdminCommercialController
 * @package App\Controller\Admin
 *
 * @Route("/api/admin")
 */
final class AdminCommercialController extends BaseController
{

    /**
     * AdminCommercialController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @SWG\Post(
     *     path="/api/admin/commercials",
     *     summary="Creates commercial",
     *     description="Creates commercail",
     *     tags={"Commercials"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Created commercial",
     *     @Model(type=Commercial::class, groups={"commercial.get"}),
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
     *          description="User has no permissions"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *     @SWG\Parameter(
     *         description="Image to upload",
     *         in="body",
     *         name="image",
     *         required=false,
     *         @SWG\Schema(
     *              type="file"
     *          )
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="commerical",
     *          @Model(type=CommercialCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"commercial.get"})
     * @Route("/commercials", methods={"POST"})
     *
     * @param Request $request
     * @return ApiView
     *
     */
    public function createAction(Request $request): ApiView
    {
        $form = $this->createForm(CommercialCreateType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        $image = $request->files->get('image');

        /** @var Commercial $commercial */
        $commercial = $form->getData();
        $commercial->setImageFile($image);

        $this->em->persist($commercial);
        $this->em->flush();

        return ApiView::create($commercial);
    }

    /**
     * @SWG\Post(
     *     path="/api/admin/commercials/{commercial}",
     *     summary="Updated commercial",
     *     description="Updated commercail",
     *     tags={"Commercials"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Updated commercial",
     *     @Model(type=Commercial::class, groups={"commercial.get"}),
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
     *          description="User has no permissions"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *     @SWG\Parameter(
     *         description="Image to upload",
     *         in="body",
     *         name="image",
     *         required=false,
     *         @SWG\Schema(
     *              type="file"
     *          )
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="commerical",
     *          @Model(type=CommercialCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"commercial.get"})
     * @Route("/commercials/{commercial}", methods={"POST"})
     *
     * @param Request $request
     * @param Commercial $commercial
     *
     * @return ApiView
     */
    public function updateAction(Request $request, Commercial $commercial): ApiView
    {
        $form = $this->createForm(CommercialCreateType::class, $commercial);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        $image = $request->files->get('image');

        /** @var Commercial $commercial */
        $commercial = $form->getData();

        if (null !== $image) {
            $commercial->setImageFile($image);
        }

        $this->em->persist($commercial);
        $this->em->flush();

        return ApiView::create($commercial);
    }

    /**
     * @SWG\Delete(
     *     path="/api/admin/commercials/{commercial}",
     *     summary="Remove commercial",
     *     description="Remove commercial",
     *     tags={"Commercials"},
     *     @SWG\Response(
     *         response= 204,
     *         description="Returns empty response"
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorised"
     *     ),
     *     @SWG\Response(
     *          response = 403,
     *          description="User has no permissions"
     *     ),
     *     @SWG\Response(
     *          response = 404,
     *          description="Commercial not found"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *   )
     * )
     *
     * @ViewAnnotation(statusCode=204)
     * @Route("/commercials/{commercial}", methods={"DELETE"})
     *
     * @param Commercial $commercial
     * @return ApiView
     */
    public function deleteAction(Commercial $commercial): ApiView
    {
        $this->em->remove($commercial);
        $this->em->flush();

        return ApiView::create([], Response::HTTP_NO_CONTENT);
    }


}