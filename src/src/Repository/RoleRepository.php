<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{

    /**
     * RoleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param string $search
     * @return array<Role>
     */
    public function findForAutoComplete(string $search = ''): array
    {
        $qb = $this->createQueryBuilder('r');

        if ('' !== $search) {
            $qb->andWhere($qb->expr()->like('r.name', ':value') . ' OR ' . $qb->expr()->like('r.code', ':value'))
                ->setParameter('value', $search . '%');
        }

        return $qb->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }


}
