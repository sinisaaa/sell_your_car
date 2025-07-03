<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArticleArticleCategoryField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleArticleCategoryField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleArticleCategoryField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleArticleCategoryField[]    findAll()
 * @method ArticleArticleCategoryField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleArticleCategoryFieldRepository extends ServiceEntityRepository
{

    /**
     * ArticleArticleCategoryFieldRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleArticleCategoryField::class);
    }

}
