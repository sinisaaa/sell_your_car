<?php

declare(strict_types=1);

namespace App\Helper\QueryFilter;

use App\Helper\QueryFilter\Filters\AbstractFilter;
use InvalidArgumentException;

class QueryFilterRegister
{
    /**
     * @var array<AbstractFilter>
     */
    private array $filters = [];

    /**
     * @param AbstractFilter $filter
     */
    public function addFilter(AbstractFilter $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * @param string $type
     * @return AbstractFilter
     */
    public function loadFilter(string $type): AbstractFilter
    {
        /** @var AbstractFilter $filter */
        foreach ($this->filters as $filter) {
            if ($filter->supportsType($type)) {
                return $filter;
            }
        }

        throw new InvalidArgumentException('Selected filter not found');
    }
}