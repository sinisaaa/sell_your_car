<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArticleCategoryFieldOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleCategoryFieldOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleCategoryFieldOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleCategoryFieldOption[]    findAll()
 * @method ArticleCategoryFieldOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleCategoryFieldOptionRepository extends ServiceEntityRepository
{

    /**
     * ArticleCategoryFieldOptionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleCategoryFieldOption::class);
    }
}
