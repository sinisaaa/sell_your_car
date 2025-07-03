<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"location.get"})
     * @var int|null
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"location.get"})
     * @var string
     */
    private string $city;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="locations")
     * @Groups({"location_region.get"})
     * @var Region|null
     */
    private ?Region $region;

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
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Region|null
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * @param Region|null $region
     * @return $this
     */
    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @param string $city
     * @param Region|null $region
     * @return Location
     */
    public static function create(string $city, ?Region $region = null): Location
    {
        $location = new self();

        return $location->setCity($city)
            ->setRegion($region);
    }
}
