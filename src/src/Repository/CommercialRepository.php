<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Commercial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commercial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commercial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commercial[]    findAll()
 * @method Commercial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommercialRepository extends ServiceEntityRepository
{

    /**
     * CommercialRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commercial::class);
    }

    /**
     * @param string $position
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findMaxPosition(string $position): int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('MAX(c.positionNumber) as maxPosition')
            ->where('c.position = :position');
        $qb->setParameters(['position' => $position]);

        return (int)$qb->getQuery()->getSingleResult()['maxPosition'];
    }

    /**
     *
     */
    public function setAllShownToNull(): void
    {
        $this->createQueryBuilder('c')
            ->update(Commercial::class, 'c')
            ->set('c.shownOn', ':null')
            ->setParameter('null',null)
            ->getQuery()
            ->execute();
    }

}
