<?php

declare(strict_types=1);

namespace App\Helper\QueryFilter\Filters;

use Doctrine\ORM\QueryBuilder;

class IsNullFilter extends AbstractFilter
{

    /**
     * @param QueryBuilder $qb
     * @param string $field
     * @param mixed $value
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $qb, string $field, mixed $value): QueryBuilder
    {
        return $value ? $qb->andWhere($qb->expr()->isNull($field)) : $qb->andWhere($qb->expr()->isNotNull($field));
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supportsType(string $type): bool
    {
        return $type === 'isNull';
    }

}