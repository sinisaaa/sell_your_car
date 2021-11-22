<?php

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserForgotPasswordToken::class);
    }

    // /**
    //  * @return UserForgotPasswordToken[] Returns an array of UserForgotPasswordToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserForgotPasswordToken
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
