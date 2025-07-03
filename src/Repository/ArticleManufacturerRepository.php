<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArticleManufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleManufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleManufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleManufacturer[]    findAll()
 * @method ArticleManufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleManufacturerRepository extends ServiceEntityRepository
{

    /**
     * ArticleManufacturerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleManufacturer::class);
    }
}
