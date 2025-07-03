<?php

declare(strict_types=1);

namespace App\Helper\CustomQuery;

class GroupByClause
{

    /**
     * GroupByClause constructor.
     * @param string $field
     */
    public function __construct(private string $field)
    {
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

}