<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecordedEventRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass=RecordedEventRepository::class)
 */
class RecordedEvent
{

    public const REGISTRATION_EVENT = 'registration';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $ipAddress;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        if (false === in_array($type, self::getValidTypes())) {
            throw new InvalidArgumentException('Event type is invalid');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     * @return $this
     */
    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getValidTypes(): array
    {
        return [self::REGISTRATION_EVENT];
    }

    /**
     * @param string $type
     * @param string $ipAddress
     * @return static
     */
    public static function create(string $type, string $ipAddress): self
    {
        $event = new self();
        return $event->setType($type)
            ->setIpAddress($ipAddress)
            ->setCreatedAt(new DateTime());
    }
}
