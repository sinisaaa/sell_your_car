<?php

declare(strict_types=1);

namespace App\Helper\QueryFilter\Filters;

use Doctrine\ORM\QueryBuilder;

class HasOneFilter extends AbstractFilter
{

    /**
     * @param QueryBuilder $qb
     * @param string $field
     * @param mixed $value
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $qb, string $field, mixed $value): QueryBuilder
    {
        $param = $this->generateParamName($field);
        return $qb->andWhere("{$field} IN (:{$param})")->setParameter($param, $value);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supportsType(string $type): bool
    {
        return $type === 'hasOne';
    }
}