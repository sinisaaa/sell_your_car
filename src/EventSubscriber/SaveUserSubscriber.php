<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\EncodeService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SaveUserSubscriber implements EventSubscriber
{

    /**
     * SaveUserSubscriber constructor.
     */
    public function __construct(private EncodeService $encodeService)
    {
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        /** @var User $entity */
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        if (null === $entity->getPassword()) {
            $this->encodeService->encodeUserPassword($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        /** @var User $entity */
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        if (null === $entity->getPassword()) {
            $this->encodeService->encodeUserPassword($entity);
        }

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return ['prePersist', 'preUpdate'];
    }
}