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
     * @return User|null
     */
    protected function getNullableUser(): ?User
    {
        /** @var User|null $user */
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

    /**
     * @param string|null $globalFilter
     * @return array
     */
    public function parseGlobalFilter(?string $globalFilter): array
    {
        if (null === $globalFilter) {
            return [
                'globalFilterDQLQuery' => null,
                'globalFilterDQLQueryParams' => null
            ];
        }

        $globalFilterDQLQuery = null;
        $globalFilterDQLParams  = null;

        $filterArray = explode(' ', $globalFilter);
        $globalFilterStrings = $this->permuteFilterItems($filterArray);
        $globalFilterStrings = array_filter($globalFilterStrings);

        if (0 < $globalFilterStrings) {
            foreach ($globalFilterStrings as $i => $globalFilterString) {
                $operator = $i === 0 ? '' : 'OR';
                $globalFilterDQLQuery .= $operator . ' a.title LIKE ' . ':title' . $i . ' ';
                $globalFilterDQLParams['title' . $i] = '%' . $globalFilterString . '%';
            }
        }

        return [
            'globalFilterDQLQuery' => $globalFilterDQLQuery,
            'globalFilterDQLQueryParams' => $globalFilterDQLParams
        ];
    }

    /**
     * @param array $items
     * @param array $perms
     * @param array $result
     * @return array
     */
    private function permuteFilterItems(array $items, array $perms = [], &$result = []): array
    {
        if (empty($items)) {
            $result[] = implode(' ', $perms) ;
        }

        for ($i = count($items) - 1; $i >= 0; --$i) {
            $newItems = $items;
            $newPerms = $perms;
            [$perm] = array_splice($newItems, $i, 1);
            array_unshift($newPerms, $perm);
            $this->permuteFilterItems($newItems, $newPerms, $result);
        }

        return $result;
    }

}