<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{

    /**
     * ChatRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    /**
     * @param User $user
     * @return Query
     */
    public function findAllByUser(User $user): Query
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX($qb->expr()->eq('c.sender', ':user'), $qb->expr()->eq('c.deletedBySender', 0)),
                $qb->expr()->andX($qb->expr()->eq('c.receiver', ':user'), $qb->expr()->eq('c.deletedByReceiver', 0)
            )))
            ->setParameter(':user', $user)
            ->orderBy('c.id', 'DESC')
            ->getQuery();
    }

}
