<?php

declare(strict_types=1);

namespace App\Helper\QueryFilter\Filters;

use App\Helper\QueryFilter\Interfaces\FilterInterface;
use App\Helper\QueryFilter\Interfaces\SupportsType;

abstract class AbstractFilter implements SupportsType, FilterInterface
{

    /**
     * @param string $field
     * @return string
     */
    protected function generateParamName(string $field): string
    {
        return $this->getFieldNoAlias($field) . 'Value';
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getFieldNoAlias(string $field): string
    {
        $value = $field;
        if (str_contains($field, '.')) {
            $array = explode('.', $field);
            $value = $array[1];
        }
        return $value;
    }

}