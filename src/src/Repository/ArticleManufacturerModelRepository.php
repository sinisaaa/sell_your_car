<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ArticleManufacturerModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleManufacturerModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleManufacturerModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleManufacturerModel[]    findAll()
 * @method ArticleManufacturerModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleManufacturerModelRepository extends ServiceEntityRepository
{

    /**
     * ArticleManufacturerModelRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleManufacturerModel::class);
    }

}
