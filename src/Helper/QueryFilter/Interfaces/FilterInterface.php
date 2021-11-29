<?php

namespace App\Helper\QueryFilter\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{

    /**
     * @param QueryBuilder $qb
     * @param string $field
     * @param mixed $value
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $qb, string $field,  mixed$value): QueryBuilder;

}