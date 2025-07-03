<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleManufacturerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleManufacturerRepository::class)
 */
class ArticleManufacturer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     * @Groups({"article_manufacturer.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_manufacturer.get"})
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class, inversedBy="articleManufacturers")
     */
    private ArticleCategory $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"article_manufacturer.get"})
     */
    private ?int $orderCategory = 0;

    /**
     * @ORM\OneToMany(targetEntity=ArticleManufacturerModel::class, mappedBy="manufacturer")
     * @Groups({"article_manufacturer_models.get"})
     */
    private Collection $articleManufacturerModels;

    public function __construct()
    {
        $this->articleManufacturerModels = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return ArticleManufacturer
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
     * @return ArticleCategory
     */
    public function getCategory(): ArticleCategory
    {
        return $this->category;
    }

    /**
     * @param ArticleCategory $category
     * @return $this
     */
    public function setCategory(ArticleCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderCategory(): ?int
    {
        return $this->orderCategory;
    }

    /**
     * @param int|null $orderCategory
     * @return $this
     */
    public function setOrderCategory(?int $orderCategory): self
    {
        $this->orderCategory = $orderCategory;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getArticleManufacturerModels(): Collection
    {
        return $this->articleManufacturerModels;
    }

    /**
     * @param ArticleManufacturerModel $articleManufacturerModel
     * @return $this
     */
    public function addArticleManufacturerModel(ArticleManufacturerModel $articleManufacturerModel): self
    {
        if (!$this->articleManufacturerModels->contains($articleManufacturerModel)) {
            $this->articleManufacturerModels[] = $articleManufacturerModel;
            $articleManufacturerModel->setManufacturer($this);
        }

        return $this;
    }

    /**
     * @param ArticleManufacturerModel $articleManufacturerModel
     * @return $this
     */
    public function removeArticleManufacturerModel(ArticleManufacturerModel $articleManufacturerModel): self
    {
        if ($this->articleManufacturerModels->removeElement($articleManufacturerModel) && $articleManufacturerModel->getManufacturer() === $this) {
            $articleManufacturerModel->setManufacturer(null);
        }

        return $this;
    }
}
