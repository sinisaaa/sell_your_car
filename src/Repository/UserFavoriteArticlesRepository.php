<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserFavoriteArticles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserFavoriteArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFavoriteArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFavoriteArticles[]    findAll()
 * @method UserFavoriteArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFavoriteArticlesRepository extends ServiceEntityRepository
{

    /**
     * UserFavoriteArticlesRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFavoriteArticles::class);
    }

}
