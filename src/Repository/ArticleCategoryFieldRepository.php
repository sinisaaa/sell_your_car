<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArticleCategoryField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleCategoryField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleCategoryField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleCategoryField[]    findAll()
 * @method ArticleCategoryField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleCategoryFieldRepository extends ServiceEntityRepository
{

    /**
     * ArticleCategoryFieldRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleCategoryField::class);
    }
}
