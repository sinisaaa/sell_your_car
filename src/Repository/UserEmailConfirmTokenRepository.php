<?php

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEmailConfirmToken::class);
    }

    // /**
    //  * @return UserEmailConfirmToken[] Returns an array of UserEmailConfirmToken objects
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
    public function findOneBySomeField($value): ?UserEmailConfirmToken
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
