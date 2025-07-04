<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserEmailConfirmToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserEmailConfirmToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEmailConfirmToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEmailConfirmToken[]    findAll()
 * @method UserEmailConfirmToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEmailConfirmTokenRepository extends ServiceEntityRepository
{

    /**
     * UserEmailConfirmTokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEmailConfirmToken::class);
    }

}
