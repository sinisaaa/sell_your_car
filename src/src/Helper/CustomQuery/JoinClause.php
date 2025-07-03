<?php

declare(strict_types=1);

namespace App\Helper\CustomQuery;

class JoinClause
{

    /**
     * JoinClause constructor.
     * @param string $join
     * @param string $alias
     * @param string $type
     * @param string|null $condition
     */
    public function __construct(
        private string $join,
        private string $alias,
        private string $type = 'ON',
        private ?string $condition = null
    ) {
    }

    /**
     * @return string
     */
    public function getJoin(): string
    {
        return $this->join;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

}