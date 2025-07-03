<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RecordedEvent;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecordedEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecordedEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecordedEvent[]    findAll()
 * @method RecordedEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordedEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecordedEvent::class);
    }

    /**
     * @param string $type
     * @param string $ipAddress
     * @param DateTime $sinceDate
     * @return RecordedEvent[]
     */
    public function findByTypeIPAndSinceDate(string $type, string $ipAddress, DateTime $sinceDate): array
    {
        return $this->createQueryBuilder('er')
            ->andWhere('er.type = :type')
            ->andWhere('er.ipAddress = :ipAddress')
            ->andWhere('er.createdAt >= :since')
            ->setParameters(['type' => $type, 'ipAddress' => $ipAddress, 'since' => $sinceDate])
            ->getQuery()
            ->getResult()
        ;
    }

}
