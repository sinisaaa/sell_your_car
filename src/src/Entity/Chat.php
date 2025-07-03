<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChatRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"chat.get"})
     * @var int|null
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Groups({"chat.get"})
     * @var string|null
     */
    private ?string $subject;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"chat.get"})
     * @var DateTimeInterface
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"chat.get"})
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"chat_sender.get"})
     * @var User|null
     */
    private ?User $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"chat_receiver.get"})
     * @var User|null
     */
    private ?User $receiver;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    private bool $deletedBySender = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    private bool $deletedByReceiver = false;

    /**
     * @ORM\OneToMany(targetEntity=ChatMessage::class, mappedBy="chat")
     * @var Collection<int, ChatMessage>
     * @Groups({"chat_message.get"})
     */
    private Collection $messages;

    /**
     * @var bool
     * @Groups({"chat.get"})
     */
    private bool $seen = false;

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

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
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return $this
     */
    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getSender(): ?User
    {
        return $this->sender;
    }

    /**
     * @param User|null $sender
     * @return $this
     */
    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    /**
     * @param User|null $receiver
     * @return $this
     */
    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDeletedBySender(): bool
    {
        return $this->deletedBySender;
    }

    /**
     * @param bool $deletedBySender
     * @return $this
     */
    public function setDeletedBySender(bool $deletedBySender): self
    {
        $this->deletedBySender = $deletedBySender;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDeletedByReceiver(): bool
    {
        return $this->deletedByReceiver;
    }

    /**
     * @param bool $deletedByReceiver
     * @return $this
     */
    public function setDeletedByReceiver(bool $deletedByReceiver): self
    {
        $this->deletedByReceiver = $deletedByReceiver;

        return $this;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @param ChatMessage $message
     * @return $this
     */
    public function addMessage(ChatMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setChat($this);
        }

        return $this;
    }

    /**
     * @param ChatMessage $message
     * @return $this
     */
    public function removeMessage(ChatMessage $message): self
    {
        if ($this->messages->removeElement($message) && $message->getChat() === $this) {
            $message->setChat(null);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function allMessagesSeenBySender(): bool
    {
        $unSeenMessages = $this->getMessages()->filter(function(ChatMessage $message) {
            return false === $message->getSeen() && $message->getReceiver() === $this->getSender();
        });

        return $unSeenMessages->count() > 0;
    }

    /**
     * @return bool
     */
    public function allMessagesSeenByReceiver(): bool
    {
        $unSeenMessages = $this->getMessages()->filter(function(ChatMessage $message) {
            return false === $message->getSeen() && $message->getReceiver() === $this->getReceiver();
        });

        return $unSeenMessages->count() > 0;
    }

    /**
     * @return bool
     */
    public function isSeen(): bool
    {
        return $this->seen;
    }

    /**
     * @param bool $seen
     * @return Chat
     */
    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }

}
