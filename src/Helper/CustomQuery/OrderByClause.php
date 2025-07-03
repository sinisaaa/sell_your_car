<?php

declare(strict_types=1);

namespace App\Helper\CustomQuery;

class OrderByClause
{

    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * OrderByClause constructor.
     * @param string $field
     * @param string $direction
     */
    public function __construct(private string $field, private string $direction = 'ASC')
    {
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

}