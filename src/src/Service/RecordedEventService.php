<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RecordedEvent;
use App\Helper\IPAddressHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class RecordedEventService
{

    /**
     * EventRecorderService constructor.
     * @param EntityManagerInterface $em
     * @param string $eventWaitingTime
     */
    public function __construct(private EntityManagerInterface $em, private string $eventWaitingTime)
    {
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasRecordedEvents(string $type): bool
    {
        $recordedEvents = $this->getRecordedEvents($type);

        return count($recordedEvents) > 0;
    }

    /**
     * @param string $type
     * @return RecordedEvent[]
     */
    public function getRecordedEvents(string $type): array
    {
        $ipAddress = IPAddressHelper::getCurrentUserIpAddress();
        $sinceDate = (new DateTime())->modify($this->eventWaitingTime);

        return $this->em->getRepository(RecordedEvent::class)->findByTypeIPAndSinceDate(
            $type, $ipAddress, $sinceDate
        );
    }

}