<?php

declare(strict_types=1);

namespace App\Helper\CustomQuery;

class SelectClause
{

    /**
     * GroupByClause constructor.
     * @param array $fields
     */
    public function __construct(private array $fields)
    {
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

}