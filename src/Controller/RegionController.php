<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;
use App\Entity\Region;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\View\View as ApiView;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Location;

final class RegionController extends BaseController
{

    /**
     * RegionController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/public/regions",
     *   summary="List of all regions",
     *   tags={"Regions"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of all regions",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Region::class, groups={"region.get"})),
     *      )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"region.get"})
     * @Route("/api/public/regions", methods={"GET"})
     *
     * @return ApiView
     */
    public function getAllAction(): ApiView
    {
        return ApiView::create([
            'items' => $this->em->getRepository(Region::class)->findAll()
        ]);
    }

    /**
     * @SWG\Get(
     *   path="/api/public/regions/{region}/locations",
     *   summary="List of all locations in region",
     *   tags={"Regions"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of all locations in region",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Location::class, groups={"location.get"})),
     *      )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Region not found"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"location.get"})
     * @Route("/api/public/regions/{region}/locations", methods={"GET"})
     *
     * @param Region $region
     * @return ApiView
     */
    public function getLocationsAction(Region $region): ApiView
    {
        return ApiView::create([
            'items' => $region->getLocations()->toArray()
        ]);
    }

    /**
     * @SWG\Get(
     *   path="/api/public/locations",
     *   summary="List of all locations",
     *   tags={"Regions"},
     *   @SWG\Response(
     *     response=200,
     *     description="List of all locations",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Location::class, groups={"location.get"})),
     *      )
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Unauthorised"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="error"
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"location.get"})
     * @Route("/api/public/locations", methods={"GET"})
     *
     * @return ApiView
     */
    public function getAllLocationsAction(): ApiView
    {
        return ApiView::create([
            'items' => $this->em->getRepository(Location::class)->findAll()
        ]);
    }

}