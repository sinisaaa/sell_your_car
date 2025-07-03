<?php

declare(strict_types=1);

namespace App\Helper\CustomQuery;

class WhereClauseDQLFilter
{

    /**
     * WhereClauseDQLFilter constructor.
     * @param string $query
     * @param array $params
     */
    public function __construct(private string $query, private array $params = [])
    {
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

}
