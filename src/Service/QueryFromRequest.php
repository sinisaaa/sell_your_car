<?php

declare(strict_types=1);

namespace App\Service;

use App\Helper\QueryFilter\Filters\AbstractFilter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use JsonException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use InvalidArgumentException;
use RuntimeException;

class QueryFromRequest
{

    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;

    /** @var ContainerInterface */
    private ContainerInterface $container;

    /** @var ObjectRepository */
    private ObjectRepository $repository;

    /**
     * BaseModelManager constructor.
     * @param EntityManagerInterface $em
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container,
    ) {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param string $alias
     * @param string $repositoryClass
     * @return Query
     *
     * @throws JsonException
     */
    public function generate(Request $request, string $alias, string $repositoryClass): Query
    {
        $filters = $request->query->get('filters');
        $sort = $request->query->get('sort');
        $direction = $request->query->get('direction');

        $qb = $this->initQueryBuilder($alias, $repositoryClass);

        $qb = $this->addFilters($filters, $qb);
        $qb = $this->addSort($sort, $direction, $alias, $qb);

        return $qb->getQuery();
    }

    /**
     * @param string $alias
     * @param string $class
     * @return QueryBuilder
     */
    private function initQueryBuilder(string $alias, string $class): QueryBuilder
    {
        $this->repository = $this->em->getRepository($class);

        $qb = $this->em->createQueryBuilder();
        $qb->from($this->repository->getClassName(), $alias);


        return $this->generateSelect($qb, $alias);
    }

    /**
     * @param QueryBuilder $qb 
     * @param string $alias
     * @return QueryBuilder
     *
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function generateSelect(QueryBuilder $qb, string $alias): QueryBuilder
    {
        $qb->select($alias);

        return $qb;
    }

    /**
     * @param string|null $filtersJson
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function addFilters(?string $filtersJson, QueryBuilder $qb): QueryBuilder
    {
        if (null === $filtersJson) {
            return $qb;
        }

        $filters = json_decode(urldecode($filtersJson));
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $qb;
        }

        $filterRegister = $this->container->get('app.query_filter.query_filter_register');

        if (null === $filterRegister) {
            return $qb;
        }

        /** @var array $filters */
        foreach ($filters as $i => $filter) {
            /** @var AbstractFilter $filterClass */
            $filterClass = $filterRegister->loadFilter($filter->type);
            if ($filterClass !== null) {
                $filter->field = $this->removeEmbedNotiationFromField($filter->field);

                $qb = $filterClass->filter($qb, $filter->field, $filter->value);
            }
        }

        return $qb;
    }

    /**
     * @param string $field
     * @return string
     */
    private function removeEmbedNotiationFromField(string $field): string
    {
        return str_replace('embed:', '', $field);
    }

    /**
     * @param string|null $sort
     * @param string|null $direction
     * @param string $alias
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function addSort(?string $sort, ?string $direction, string $alias, QueryBuilder $qb): QueryBuilder
    {
        if (null === $sort) {
            return $qb;
        }

        $relationFields = explode('.', $sort);
        $sort = $this->getColumn($relationFields);

        $qb = $this->joinTables($relationFields, 'leftJoin', $qb);
    
        if(!$sort) {
            $qb->addOrderBy($alias . '.id', 'DESC');
        } else {
            $qb->addOrderBy($sort, $direction);
        }

        return $qb;
    }

    /**
     * @param array $relationFields
     * @param string $joinType
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function joinTables(array $relationFields, string $joinType, QueryBuilder $qb): QueryBuilder
    {
        $relationsCount = count($relationFields);

        if (3 <= $relationsCount) {
            $parentTable = $relationFields[0];
            $tableName = $relationFields[1];

            if (false === $this->isTableAlreadyJoined($qb, $tableName)) {
                $qb->$joinType($parentTable . '.' . $tableName, $tableName);
            }

            array_shift($relationFields);
            $this->joinTables($relationFields, $joinType, $qb);
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return bool
     */
    private function isTableAlreadyJoined(QueryBuilder $qb, string $alias): bool
    {
        return in_array($alias, $qb->getAllAliases());
    }

    /**
     * @param array $relationFields
     * @return string
     */
    private function getColumn(array $relationFields): string
    {
        $sortField =  implode('.', $relationFields);
        $relationFieldsCount = count($relationFields);

        if (3 <= $relationFieldsCount) {
            $sortField = $relationFields[$relationFieldsCount - 2] . '.' . $relationFields[$relationFieldsCount - 1];
        }

        return $sortField;
    }

}
