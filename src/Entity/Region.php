<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RegionRepository::class)
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"region.get"})
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"region.get"})
     * @var string
     */
    private string $country;

    /**
     * @ORM\OneToMany(targetEntity=Location::class, mappedBy="region")
     * @var Collection<Location, int>
     */
    private Collection $locations;

    /**
     * Region constructor.
     */
    public function __construct()
    {
        $this->locations = new ArrayCollection();
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
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Region
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    /**
     * @param Location $location
     * @return $this
     */
    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setRegion($this);
        }

        return $this;
    }

    /**
     * @param Location $location
     * @return $this
     */
    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location) && $location->getRegion() === $this) {
            $location->setRegion(null);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $country
     * @return Region
     */
    public static function create(string $name, string $country): Region
    {
        $region = new self();

        return $region->setName($name)
            ->setCountry($country);
    }
}
