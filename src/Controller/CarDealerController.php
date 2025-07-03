<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\User;
use App\Entity\UserRating;
use App\Form\Type\UserRatingCreateType;
use App\Helper\CustomQuery;
use App\Helper\CustomQuery\WhereClauseFilter;
use App\Service\QueryFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View as ApiView;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use Nelmio\ApiDocBundle\Annotation\Model;

final class CarDealerController extends BaseController
{

    /**
     * CarDealerController constructor.
     * @param QueryFromRequest $queryFromRequest
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private QueryFromRequest $queryFromRequest,
        private PaginatorInterface $paginator,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @SWG\Get(
     *   path="/api/public/car-dealers",
     *   summary="List of all car dealers",
     *   tags={"Car Dealers"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all car dealers",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "location.get", "location_region.get", "region.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
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
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/public/car-dealers", methods={"GET"})
     *
     * @param Request $request
     * @return array<mixed>
     *
     * @throws \JsonException
     */
    public function getAllCarDealersAction(Request $request): array
    {
        $customQuery = new CustomQuery();
        $customQuery->addAndWhere(new WhereClauseFilter('u.active', 1, WhereClauseFilter::OPERAND_EQUALS));
        $customQuery->addAndWhere(new WhereClauseFilter('u.type', User::TYPE_CAR_DEALER, WhereClauseFilter::OPERAND_EQUALS));

        $query = $this->queryFromRequest->generate($request, 'u', User::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        return $this->getPaginatedItems($pagination);
    }

    /**
     * @SWG\Get(
     *   path="/api/public/car-dealers/{carDealer}/ratings",
     *   summary="List of all car dealer ratings",
     *   tags={"Car Dealers"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of all car dealer rating",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=UserRating::class, groups={"user.rel", "user_rating.get"})),
     *          @SWG\Property(property="_meta", type="object", ref="#/definitions/MetaPagination"),
     *      )
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
     *      description="Sort query, alias is ur (ex: sort=ur.rating)",
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
     * @ViewAnnotation(serializerGroups={"user.rel", "user_rating.get"})
     * @Route("/api/public/car-dealers/{carDealer}/ratings", methods={"GET"})
     *
     * @param Request $request
     * @param User $carDealer
     * @return array<mixed>
     *
     */
    public function getAllCarDealerRatingsAction(Request $request, User $carDealer): array
    {
        $customQuery = new CustomQuery();
        $customQuery->addAndWhere(new WhereClauseFilter('ur.ratedUser', $carDealer, WhereClauseFilter::OPERAND_EQUALS));

        $query = $this->queryFromRequest->generate($request, 'ur', UserRating::class, $customQuery);

        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), $request->query->getInt('perPage', 10));

        return $this->getPaginatedItems($pagination);
    }

    /**
     * @SWG\Post(
     *     path="/api/car-dealers/{carDealer}/rate",
     *     summary="Rates car dealer",
     *     description="Rates car dealer",
     *     tags={"Car Dealers"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Returns rated car dealer",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "location.get", "location_region.get", "region.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 400,
     *          description="Form validation error"
     *     ),
     *     @SWG\Response(
     *          response = 409,
     *          description="User is already rated, trying to rate self or trying to rate standard user"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="userRating",
     *          @Model(type=UserRatingCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/car-dealers/{carDealer}/rate", methods={"POST"})
     *
     * @param Request $request
     * @param User $carDealer
     * @return ApiView
     */
    public function rateAction(Request $request, User $carDealer): ApiView
    {
        $form = $this->createForm(UserRatingCreateType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return ApiView::create($form);
        }

        if (User::TYPE_CAR_DEALER !== $carDealer->getType()) {
            throw new ConflictHttpException($this->translator->trans('Exception.User.Rated.User.Not.Car.Dealer'));
        }

        if ($this->getUser()->getId() === $carDealer->getId()) {
            throw new ConflictHttpException($this->translator->trans('Exception.User.Can.Not.Rate.Self'));
        }

        if (null !== $this->em->getRepository(UserRating::class)->findByUserAndRatedUser($this->getUser(), $carDealer)) {
            throw new ConflictHttpException($this->translator->trans('Exception.User.You.Already.Rated.Car.Dealer'));
        }

        /** @var UserRating $userRating */
        $userRating = $form->getData();
        $userRating->setUser($this->getUser());
        $userRating->setRatedUser($carDealer);

        $this->em->persist($userRating);
        $this->em->flush();

        return ApiView::create($carDealer);
    }

    /**
     * @SWG\Delete(
     *     path="/api/car-dealers/{carDealer}/rate",
     *     summary="Remove car dealer rating",
     *     description="Remove car dealer rating",
     *     tags={"Car Dealers"},
     *     @SWG\Response(
     *     response= 200,
     *     description="Returns car dealer",
     *     @SWG\Schema(
     *          @SWG\Property(property="items", type="array", @Model(type=User::class, groups={"user.get", "user_location.get", "location.get", "location_region.get", "region.get"})),
     *          )
     *     ),
     *     @SWG\Response(
     *          response = 404,
     *          description="User rating not found"
     *     ),
     *     @SWG\Response(
     *            response="default",
     *            description="error"
     *        ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="userRating",
     *          @Model(type=UserRatingCreateType::class)
     *     )
     *   )
     * )
     *
     * @ViewAnnotation(serializerGroups={"user.get", "user_location.get", "location.get", "location_region.get", "region.get"})
     * @Route("/api/car-dealers/{carDealer}/rate", methods={"DELETE"})
     *
     * @param User $carDealer
     * @return ApiView
     */
    public function deleteRatingAction(User $carDealer): ApiView
    {
        if (null === $userRating = $this->em->getRepository(UserRating::class)->findByUserAndRatedUser($this->getUser(), $carDealer)) {
            throw new NotFoundHttpException($this->translator->trans('Exception.User.Car.Dealer.Not.Rated'));
        }

        $this->em->remove($userRating);
        $this->em->flush();

        return ApiView::create($carDealer);
    }

}