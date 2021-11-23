<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatMessage[]    findAll()
 * @method ChatMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMessage::class);
    }

    /**
     * @param Chat $chat
     * @param User $receiver
     */
    public function markAllMessagesAsSeen(Chat $chat, User $receiver): void
    {
        $qb = $this->createQueryBuilder('cm');
        $qb = $qb->update(ChatMessage::class, 'cm')
            ->where('cm.chat = :chat')
            ->andWhere('cm.receiver = :receiver')
            ->set('cm.seen', true)
            ->setParameters(['chat' => $chat, 'receiver' => $receiver])
            ->getQuery();

        $qb->execute();
    }

}
