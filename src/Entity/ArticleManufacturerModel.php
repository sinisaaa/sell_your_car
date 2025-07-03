<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleManufacturerModelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleManufacturerModelRepository::class)
 */
class ArticleManufacturerModel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     * @Groups({"article_manufacturer_model.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_manufacturer_model.get"})
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleManufacturer::class, inversedBy="articleManufacturerModels")
     * @ORM\JoinColumn(nullable=false)
     */
    private ArticleManufacturer $manufacturer;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return ArticleManufacturerModel
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
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
     * @return ArticleManufacturer
     */
    public function getManufacturer(): ArticleManufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param ArticleManufacturer $manufacturer
     * @return $this
     */
    public function setManufacturer(ArticleManufacturer $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }
}
