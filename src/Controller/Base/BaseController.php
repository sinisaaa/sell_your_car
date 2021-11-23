<?php

declare(strict_types=1);

namespace App\Controller\Base;

use App\Entity\User;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{

    /**
     * @return User
     */
    protected function getUser(): User
    {
        /** @var User $user */
        $user =  parent::getUser();

        return $user;
    }

    /**
     * @param SlidingPagination $pagination
     * @param mixed $data
     * @return array
     */
    public function getPaginatedItems(SlidingPagination $pagination, mixed $data = null): array
    {
        return [
            'items' => $data ?? $pagination->getItems(),
            '_meta' => [
                'totalCount' => $pagination->getTotalItemCount(),
                'pageCount' => $pagination->getPageCount(),
                'current' => $pagination->getPage() ? (int) $pagination->getPage() : 1,
                'numItemsPerPage' => $pagination->getItemNumberPerPage(),
            ],
        ];
    }

}