<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\RecordedEvent;
use App\Event\UserRegisteredEvent;
use App\Helper\IPAddressHelper;
use Doctrine\ORM\EntityManagerInterface;

final class EventRecorderListener
{

    /**
     * EventRecorderListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @param UserRegisteredEvent $event
     */
    public function recordUserRegisteredEvent(UserRegisteredEvent $event): void
    {
        $eventAction = RecordedEvent::create(RecordedEvent::REGISTRATION_EVENT, IPAddressHelper::getCurrentUserIpAddress());

        $this->em->persist($eventAction);
        $this->em->flush();
    }

}