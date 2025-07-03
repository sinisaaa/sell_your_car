<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserForgotPasswordToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserForgotPasswordToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserForgotPasswordToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserForgotPasswordToken[]    findAll()
 * @method UserForgotPasswordToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserForgotPasswordTokenRepository extends ServiceEntityRepository
{

    /**
     * UserForgotPasswordTokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserForgotPasswordToken::class);
    }

}
