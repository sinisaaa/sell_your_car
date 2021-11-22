<?php

namespace App\Entity;

use App\Helper\TokenHelper;
use App\Repository\UserForgotPasswordTokenRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=UserForgotPasswordTokenRepository::class)
 */
class UserForgotPasswordToken
{

    private const TOKEN_VALIDITY_DURATION = 1800;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private string $token;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @var User
     */
    private User $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        return ($this->getCreatedAt()->getTimestamp() + self::TOKEN_VALIDITY_DURATION < time());
    }

    /**
     * @param User $user
     * @return UserForgotPasswordToken
     *
     * @throws Exception
     */
    public static function create(User $user): UserForgotPasswordToken
    {
        $token = new self();
        return $token->setUser($user)
            ->setCreatedAt(new DateTime())
            ->setToken(TokenHelper::generateToken());
    }
}
