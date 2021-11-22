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

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.get"})
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.get"})
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @Groups({"user.get"})
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
     * @var boolean
     */
    private bool $active = false;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class)
     * @Groups({"user_location.get"})
     * @var Location|null
     */
    private ?Location $location;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
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

}
