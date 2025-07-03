<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRatingRepository::class)
 */
class UserRating
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user_rating.get"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"user_rating.get"})
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userRatings")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?User $ratedUser = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user_rating.get"})
     */
    private int $rating;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user_ratings.get"})
     */
    private ?string $comment = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return User|null
     */
    public function getRatedUser(): ?User
    {
        return $this->ratedUser;
    }

    /**
     * @param User|null $ratedUser
     * @return $this
     */
    public function setRatedUser(?User $ratedUser): self
    {
        $this->ratedUser = $ratedUser;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @return $this
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
