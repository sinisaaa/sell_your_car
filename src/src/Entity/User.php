<?php

declare(strict_types=1);

namespace App\Entity;

use App\Helper\ValueObjects\RoleCode;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface
{

    public const TYPE_USER = 'user';
    public const TYPE_CAR_DEALER = 'car_dealer';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user.get", "user.rel"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.get", "user.rel"})
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user.get", "user.rel"})
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user.get"})
     * @var string
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @var string|null
     */
    private ?string $salt;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @Assert\NotBlank(message="Account.Password.Empty", allowNull=true)
     * @Assert\Length(
     *      min = 8,
     *      minMessage = "Password.Min.Length",
     * )
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*\s).+$/", message="Password.Requirement")
     *
     * @var string|null
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user.get"})
     * @var string|null
     */
    private ?string $phone = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user.get", "user.rel"})
     * @var string|null
     */
    private ?string $address = null;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private DateTime $createdOn;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class)
     * @Groups({"user_roles.get"})
     * @var Collection<Role, int>
     */
    private Collection $roles;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $lastLogin = null;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"user.get"})
     * @var boolean
     */
    private bool $active = true;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class)
     * @Groups({"user_location.get"})
     * @var Location|null
     */
    private ?Location $location = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.get"})
     */
    private string $type = self::TYPE_USER;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="user")
     * @var Collection
     */
    private Collection $articles;

    /**
     * @ORM\OneToMany(targetEntity=UserRating::class, mappedBy="ratedUser")
     * @Groups({"user_ratings.get"})
     */
    private Collection $userRatings;

    /**
     * @ORM\OneToMany(targetEntity=UserFavoriteArticles::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $userFavoriteArticles;

    /**
     * @ORM\OneToMany(targetEntity=Search::class, mappedBy="user")
     */
    private Collection $searches;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user_credits.get"})
     */
    private int $activeCredits = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user_credits.get"})
     */
    private int $passiveCredits = 0;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"user_notifications.get"})
     */
    private bool $smsNotifications = true;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"user_notifications.get"})
     */
    private bool $pushNotifications = true;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"user_notifications.get"})
     */
    private bool $discountNotification = true;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"user_notifications.get"})
     */
    private bool $sellNotification = true;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"user_notifications.get"})
     */
    private bool $buyNotifications = true;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"user.get"})
     */
    private bool $emailVerified = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $pikName = null;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->userRatings = new ArrayCollection();
        $this->userFavoriteArticles = new ArrayCollection();
        $this->searches = new ArrayCollection();
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return $this
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return $this
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @param null|string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    /**
     * @param DateTime $createdOn
     * @return User
     */
    public function setCreatedOn(DateTime $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string|null $salt
     * @return User
     */
    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return User
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [RoleCode::USER];

        foreach ($this->roles as $role) {
            $roles[] = $role->getCode();
        }

        return array_unique($roles);
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->lastLogin;
    }

    /**
     * @param DateTimeInterface|null $lastLogin
     * @return $this
     */
    public function setLastLogin(?DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

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
     * @return string
     */
    public function getType(): string
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
            throw new \InvalidArgumentException('User type is invalid');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article) && $article->getUser() === $this) {
            $article->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserRatings(): Collection
    {
        return $this->userRatings;
    }

    /**
     * @param UserRating $userRating
     * @return $this
     */
    public function addUserRating(UserRating $userRating): self
    {
        if (!$this->userRatings->contains($userRating)) {
            $this->userRatings[] = $userRating;
            $userRating->setRatedUser($this);
        }

        return $this;
    }

    /**
     * @param UserRating $userRating
     * @return $this
     */
    public function removeUserRating(UserRating $userRating): self
    {
        if ($this->userRatings->removeElement($userRating) && $userRating->getRatedUser() === $this) {
            $userRating->setRatedUser(null);
        }

        return $this;
    }

    /**
     * @return float|null
     * @Groups({"user.get"})
     */
    public function getAverageRating(): ?float
    {
        $totalRatings = null;

        /** @var UserRating $userRating */
        foreach ($this->userRatings as $userRating) {
            $totalRatings += $userRating->getRating();
        }

        return null !== $totalRatings ? $totalRatings/$this->userRatings->count() : null;
    }

    /**
     * @return string[]
     */
    private static function getValidTypes(): array
    {
        return [self::TYPE_USER, self::TYPE_CAR_DEALER];
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array(RoleCode::ADMIN, $this->getRoles(), true);
    }

    /**
     * @return Collection
     */
    public function getUserFavoriteArticles(): Collection
    {
        return $this->userFavoriteArticles;
    }

    /**
     * @param UserFavoriteArticles $userFavoriteArticle
     * @return $this
     */
    public function addUserFavoriteArticle(UserFavoriteArticles $userFavoriteArticle): self
    {
        if (!$this->userFavoriteArticles->contains($userFavoriteArticle)) {
            $this->userFavoriteArticles[] = $userFavoriteArticle;
            $userFavoriteArticle->setUser($this);
        }

        return $this;
    }

    /**
     * @param UserFavoriteArticles $userFavoriteArticle
     * @return $this
     */
    public function removeUserFavoriteArticle(UserFavoriteArticles $userFavoriteArticle): self
    {
        if ($this->userFavoriteArticles->removeElement($userFavoriteArticle) && $userFavoriteArticle->getUser() === $this) {
            $userFavoriteArticle->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSearches(): Collection
    {
        return $this->searches;
    }

    /**
     * @param Search $search
     * @return $this
     */
    public function addSearch(Search $search): self
    {
        if (!$this->searches->contains($search)) {
            $this->searches[] = $search;
            $search->setUser($this);
        }

        return $this;
    }

    /**
     * @param Search $search
     * @return $this
     */
    public function removeSearch(Search $search): self
    {
        if ($this->searches->removeElement($search) && $search->getUser() === $this) {
            $search->setUser(null);
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getActiveCredits(): ?int
    {
        return $this->activeCredits;
    }

    /**
     * @param int $activeCredits
     * @return $this
     */
    public function setActiveCredits(int $activeCredits): self
    {
        $this->activeCredits = $activeCredits;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPassiveCredits(): ?int
    {
        return $this->passiveCredits;
    }

    /**
     * @param int $passiveCredits
     * @return $this
     */
    public function setPassiveCredits(int $passiveCredits): self
    {
        $this->passiveCredits = $passiveCredits;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSmsNotifications(): bool
    {
        return $this->smsNotifications;
    }

    /**
     * @param bool $smsNotifications
     * @return $this
     */
    public function setSmsNotifications(bool $smsNotifications): self
    {
        $this->smsNotifications = $smsNotifications;

        return $this;
    }

    /**
     * @return bool
     */
    public function getPushNotifications(): bool
    {
        return $this->pushNotifications;
    }

    /**
     * @param bool $pushNotifications
     * @return $this
     */
    public function setPushNotifications(bool $pushNotifications): self
    {
        $this->pushNotifications = $pushNotifications;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDiscountNotification(): bool
    {
        return $this->discountNotification;
    }

    /**
     * @param bool $discountNotification
     * @return $this
     */
    public function setDiscountNotification(bool $discountNotification): self
    {
        $this->discountNotification = $discountNotification;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSellNotification(): bool
    {
        return $this->sellNotification;
    }

    /**
     * @param bool $sellNotification
     * @return $this
     */
    public function setSellNotification(bool $sellNotification): self
    {
        $this->sellNotification = $sellNotification;

        return $this;
    }

    /**
     * @return bool
     */
    public function getBuyNotifications(): bool
    {
        return $this->buyNotifications;
    }

    /**
     * @param bool $buyNotifications
     * @return $this
     */
    public function setBuyNotifications(bool $buyNotifications): self
    {
        $this->buyNotifications = $buyNotifications;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    /**
     * @param bool $emailVerified
     * @return $this
     */
    public function setEmailVerified(bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;
        $this->active = true;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPikName(): ?string
    {
        return $this->pikName;
    }

    /**
     * @param string|null $pikName
     * @return $this
     */
    public function setPikName(?string $pikName): self
    {
        $this->pikName = $pikName;

        return $this;
    }

}
