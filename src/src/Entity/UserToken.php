<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserTokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserTokenRepository::class)
 */
class UserToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @var User
     */
    private User $user;

    /**
     * @ORM\Column(type="text")
     * @var string
     * @Groups({"user-token.get"})
     */
    private string $token;

    /**
     * @ORM\Column(type="text")
     * @var string
     * @Groups({"user-token.get"})
     */
    private string $refreshToken;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private DateTimeInterface $generatedDate;

    public function __construct()
    {
        $this->setGeneratedDate(new \DateTime());
        $this->setRefreshToken($this->generateRefreshToken());
    }

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
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
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
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return $this
     */
    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getGeneratedDate(): DateTimeInterface
    {
        return $this->generatedDate;
    }

    /**
     * @param DateTimeInterface $generatedDate
     * @return $this
     */
    public function setGeneratedDate(DateTimeInterface $generatedDate): self
    {
        $this->generatedDate = $generatedDate;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateRefreshToken(): string
    {
        return bin2hex(random_bytes(20));
    }

    /**
     * @param int $jwtTokenTTL
     * @return bool
     */
    public function isRefreshExpired(int $jwtTokenTTL): bool
    {
        return ($this->getGeneratedDate()->getTimestamp() + $jwtTokenTTL) < time();
    }

    /**
     * @param string $generatedToken
     * @param User $user
     * @return UserToken
     */
    public static function create(string $generatedToken, User $user): UserToken
    {
        $token = new self();
        $token->setToken($generatedToken)
            ->setUser($user);

        return $token;

    }
}
