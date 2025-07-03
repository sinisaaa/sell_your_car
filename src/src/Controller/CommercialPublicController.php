<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Commercial;
use App\Service\QueryFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View as ApiView;

final class CommercialPublicController extends BaseController
{

    /**
     * CommercialController constructor.
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/public/commercials",
     *   summary="List of commercials",
     *   tags={"Commercials"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all commercials",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=Commercial::class, groups={"commercial.get"})),
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
     *      description="Sort query, alias is c (ex: sort=c.position)",
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
     *      description="Filter query, alias is c",
     *      @SWG\Schema(
     *         @SWG\Property(property="filters", type="string")
     *      )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"commercial.get"})
     * @Route("/api/public/commercials", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     */
    public function getAllAction(Request $request): array
    {
        $query = $this->queryFromRequest->generate($request, 'c', Commercial::class);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        return $this->getPaginatedItems($pagination);
    }

    /**
     * @SWG\Get(
     *     path="/api/public/commercials/main",
     *     summary="Get main page commercial for today",
     *     description="Get main page commercial for today (different every 24h)",
     *     tags={"Commercials"},
     *     @SWG\Response(
     *         response= 200,
     *         description="Today main commercial",
     *         @Model(type=Commercial::class, groups={"commercial.get"}),
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *     ),
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"commercial.get"})
     * @Route("/api/public/commercials/main", methods={"GET"})
     *
     * @param Request $request
     * @return ApiView
     *
     */
    public function getMainForToday(Request $request): ApiView
    {
        $commercial = $this->em->getRepository(Commercial::class)->findOneBy(
            ['position' => Commercial::POSITION_MAIN, 'shownOn' => (new \DateTime())->setTime(0, 0)]
        );

        if (null === $commercial) {
            $commercials = $this->em->getRepository(Commercial::class)->findBy(['position' => Commercial::POSITION_MAIN, 'shownOn' => null]);

            if (count($commercials) !== 0) {
                $commercial = $commercials[0];
                $commercial->setShownOn(new \DateTime());
                $this->em->persist($commercial);
            } else {
                $this->em->getRepository(Commercial::class)->setAllShownToNull();
                $commercials = $this->em->getRepository(Commercial::class)->findBy(['position' => Commercial::POSITION_MAIN, 'shownOn' => null]);

                if (count($commercials) > 0) {
                    $commercial = $commercials[0];
                    $commercial->setShownOn(new \DateTime());
                    $this->em->persist($commercial);
                } else {
                    $commercial = null;
                }
            }
        }

        $this->em->flush();

        return ApiView::create($commercial, Response::HTTP_OK);
    }

}