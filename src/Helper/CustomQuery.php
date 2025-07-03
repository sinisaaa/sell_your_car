<?php

namespace App\Helper;

use App\Helper\CustomQuery\GroupByClause;
use App\Helper\CustomQuery\JoinClause;
use App\Helper\CustomQuery\OrderByClause;
use App\Helper\CustomQuery\SelectClause;
use App\Helper\CustomQuery\WhereClauseDQLFilter;
use App\Helper\CustomQuery\WhereClauseFilter;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;

class CustomQuery
{
    /** @var SelectClause|null */
    private ?SelectClause $select = null;

    /** @var JoinClause[] $joins */
    private array $joins = [];

    /** @var JoinClause[] $leftJoins */
    private array $leftJoins = [];

    /** @var WhereClauseFilter[] $andWheres  */
    private array $andWheres = [];

    /** @var WhereClauseFilter[] $orWheres */
    private array $orWheres = [];

    /** @var array $andWhereDQL */
    private array $andWhereDQL = [];

    /** @var GroupByClause[] */
    private array $groupBys = [];

    /** @var OrderByClause[] */
    private array $orderBys = [];

    /**
     * @param SelectClause $select
     * @return $this
     */
    public function setSelect(SelectClause $select): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @param JoinClause $join
     * @return $this
     */
    public function addJoin(JoinClause $join): self
    {
        $this->joins[] = $join;

        return $this;
    }

    /**
     * @param JoinClause $join
     * @return $this
     */
    public function addLeftJoin(JoinClause $join): self
    {
        $this->leftJoins[] = $join;

        return $this;
    }

    /**
     * @param WhereClauseFilter $where
     * @return $this
     */
    public function addAndWhere(WhereClauseFilter $where): self
    {
        $this->andWheres[] = $where;

        return $this;
    }

    /**
     * @param WhereClauseFilter $where
     * @return $this
     */
    public function addOrWhere(WhereClauseFilter $where): self
    {
        $this->orWheres[] = $where;

        return $this;
    }

    /**
     * @param WhereClauseDQLFilter $where
     * @return $this
     */
    public function addAndWhereDQL(WhereClauseDQLFilter $where): self
    {
        $this->andWhereDQL[] = $where;
        return $this;
    }

    /**
     * @param GroupByClause $groupBy
     * @return $this
     */
    public function addGroupBy(GroupByClause $groupBy): self
    {
        $this->groupBys[] = $groupBy;

        return $this;
    }

    /**
     * @param OrderByClause $orderBy
     * @return $this
     */
    public function addOrderBy(OrderByClause $orderBy): self
    {
        $this->orderBys[] = $orderBy;

        return $this;
    }

    /**
     * Adds additional query parts for given query builder
     *
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function generateAdditionalQuery(QueryBuilder $qb): QueryBuilder
    {
        if($this->select){
            $qb->select($this->select->getFields());
        }
        foreach ($this->joins as $join) {
            $qb->join($join->getJoin(), $join->getAlias(), $join->getType(), $join->getCondition());

        }
        foreach ($this->leftJoins as $join) {
            $qb->leftJoin($join->getJoin(), $join->getAlias(), $join->getType(), $join->getCondition());
        }
        foreach ($this->andWheres as $where) {
            $qb->andWhere($this->generateWhereParams($where, $qb));
            if ($where->shouldSetParam()) {
                $qb->setParameter(':' . $where->getParam(), $where->getValue());
            }
        }
        foreach ($this->orWheres as $where) {
            $qb->orWhere($this->generateWhereParams($where, $qb));
            if ($where->shouldSetParam()) {
                $qb->setParameter(':' . $where->getParam(), $where->getValue());
            }
        }
        foreach ($this->andWhereDQL as $whereDQL) {
            $qb->andWhere($whereDQL->getQuery());
            foreach ($whereDQL->getParams() as $param => $value) {
                $qb->setParameter(':' . $param, $value);
            }
        }
        foreach ($this->groupBys as $groupBy) {
            $qb->addGroupBy($groupBy->getField());
        }
        foreach ($this->orderBys as $orderBy) {
            $qb->addOrderBy($orderBy->getField(), $orderBy->getDirection());
        }

        return $qb;
    }

    /**
     * @param WhereClauseFilter $where
     * @param QueryBuilder $qb
     *
     * @return Comparison|Func|string
     */
    private function generateWhereParams(WhereClauseFilter $where, QueryBuilder $qb): string|Func|Comparison
    {
        return match ($where->getOperator()) {
            WhereClauseFilter::OPERAND_IS_EMPTY => $where->getField() . ' is empty',
            WhereClauseFilter::OPERAND_LIKE => $qb->expr()->like($where->getField(), $qb->expr()->literal('%'. $where->getValue() . '%')),
            WhereClauseFilter::OPERAND_IN => $qb->expr()->in($where->getField(), ':' . $where->getParam()),
            WhereClauseFilter::OPERAND_NOT_IN => $qb->expr()->notIn($where->getField(), ':' . $where->getParam()),
            WhereClauseFilter::OPERAND_IS_NULL => $where->getField() . ' is null',
            WhereClauseFilter::OPERAND_IS_NOT_NULL => $where->getField() . ' is not null',
            WhereClauseFilter::OPERAND_CUSTOM => $where->getField(),
            default => $where->getField() . ' ' . $where->getOperator() . ' :' . $where->getParam(),
        };
    }

}
