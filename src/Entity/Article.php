<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"article.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article.get"})
     */
    private string $title;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"article.get"})
     */
    private ?bool $exchange = null;

    /**
     * @ORM\Column(type="decimal", nullable=true, precision=20, scale=2)
     * @Groups({"article.get"})
     */
    private ?float $price = null;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $urgent = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $fixed = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $negotiable = false;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $featured = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"article.get"})
     */
    private ?string $conditions = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"article.get"})
     */
    private ?string $telephone = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"article.get"})
     */
    private bool $discontinued = false;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"article.get"})
     */
    private bool $status = true;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"article.get"})
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"article.get"})
     */
    private ?Location $location = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"article_user.get"})
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"article.get"})
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article.get"})
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article.get"})
     */
    private ?DateTimeInterface $soldAt = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"article.get"})
     */
    private int $hits = 0;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    /**
     * @param string|null $conditions
     * @return $this
     */
    public function setConditions(?string $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    /**
     * @param bool $featured
     * @return $this
     */
    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getExchange(): ?bool
    {
        return $this->exchange;
    }

    /**
     * @param bool|null $exchange
     * @return $this
     */
    public function setExchange(?bool $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getUrgent(): ?bool
    {
        return $this->urgent;
    }

    /**
     * @param bool $urgent
     * @return $this
     */
    public function setUrgent(bool $urgent): self
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDiscontinued(): ?bool
    {
        return $this->discontinued;
    }

    /**
     * @param bool $discontinued
     * @return $this
     */
    public function setDiscontinued(bool $discontinued): self
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNegotiable(): ?bool
    {
        return $this->negotiable;
    }

    /**
     * @param bool $negotiable
     * @return $this
     */
    public function setNegotiable(bool $negotiable): self
    {
        $this->negotiable = $negotiable;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFixed(): ?bool
    {
        return $this->fixed;
    }

    /**
     * @param bool $fixed
     * @return $this
     */
    public function setFixed(bool $fixed): self
    {
        $this->fixed = $fixed;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     * @return $this
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location|null $location
     * @return $this
     */
    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

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
    public function getSoldAt(): ?DateTimeInterface
    {
        return $this->soldAt;
    }

    /**
     * @param DateTimeInterface|null $soldAt
     * @return $this
     */
    public function setSoldAt(?DateTimeInterface $soldAt): self
    {
        $this->soldAt = $soldAt;

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
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return $this
     */
    public function setHits(int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Increments hits counter
     */
    public function incrementHitsCounter(): void
    {
        $this->hits++;
    }
}
