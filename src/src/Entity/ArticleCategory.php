<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleCategoryRepository::class)
 */
class ArticleCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"article_category.get", "article_category_ref"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_category.get", "article_category_ref"})
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity=ArticleCategoryField::class, mappedBy="category")
     * @Groups({"article_category_fields.get"})
     */
    private Collection $articleCategoryFields;

    /**
     * @ORM\OneToMany(targetEntity=ArticleManufacturer::class, mappedBy="category")
     * @Groups({"article_manufacturer.get"})
     */
    private Collection $articleManufacturers;

    public function __construct()
    {
        $this->articleCategoryFields = new ArrayCollection();
        $this->articleManufacturers = new ArrayCollection();
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
     * @return Collection
     */
    public function getArticleCategoryFields(): Collection
    {
        return $this->articleCategoryFields;
    }

    /**
     * @param ArticleCategoryField $articleCategoryField
     * @return $this
     */
    public function addArticleCategoryField(ArticleCategoryField $articleCategoryField): self
    {
        if (!$this->articleCategoryFields->contains($articleCategoryField)) {
            $this->articleCategoryFields[] = $articleCategoryField;
            $articleCategoryField->setCategory($this);
        }

        return $this;
    }

    /**
     * @param ArticleCategoryField $articleCategoryField
     * @return $this
     */
    public function removeArticleCategoryField(ArticleCategoryField $articleCategoryField): self
    {
        if ($this->articleCategoryFields->removeElement($articleCategoryField) && $articleCategoryField->getCategory() === $this) {
            $articleCategoryField->setCategory(null);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getArticleManufacturers(): Collection
    {
        return $this->articleManufacturers;
    }

    /**
     * @param ArticleManufacturer $articleManufacturer
     * @return $this
     */
    public function addArticleManufacturer(ArticleManufacturer $articleManufacturer): self
    {
        if (!$this->articleManufacturers->contains($articleManufacturer)) {
            $this->articleManufacturers[] = $articleManufacturer;
            $articleManufacturer->setCategory($this);
        }

        return $this;
    }

    /**
     * @param ArticleManufacturer $articleManufacturer
     * @return $this
     */
    public function removeArticleManufacturer(ArticleManufacturer $articleManufacturer): self
    {
        if ($this->articleManufacturers->removeElement($articleManufacturer) && $articleManufacturer->getCategory() === $this) {
            $articleManufacturer->setCategory(null);
        }

        return $this;
    }
}
