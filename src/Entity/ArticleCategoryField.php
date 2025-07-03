<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleCategoryFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleCategoryFieldRepository::class)
 */
class ArticleCategoryField
{

    public const ARTICLE_CATEGORY_DROPDOWN_TYPE = 'dropdown';
    public const ARTICLE_CATEGORY_CHECKBOX_TYPE = 'checkboxes';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     * @Groups({"article_category_fields.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_category_fields.get"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_category_fields.get"})
     */
    private string $type;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class, inversedBy="articleCategoryFields")
     */
    private ArticleCategory $category;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"article_category_fields.get"})
     */
    private bool $required = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"article_category_fields.get"})
     */
    private ?string $notes = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $oldName = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"article_category_fields.get"})
     */
    private ?int $attributeOrder = null;

    /**
     * @ORM\OneToMany(targetEntity=ArticleCategoryFieldOption::class, mappedBy="field")
     * @Groups({"article_category_field_options.get"})
     */
    private Collection $articleCategoryFieldOptions;

    public function __construct()
    {
        $this->articleCategoryFieldOptions = new ArrayCollection();
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
     * @return ArticleCategoryField
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
        $this->type = $type;

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
     * @return bool|null
     */
    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return $this
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOldName(): ?string
    {
        return $this->oldName;
    }

    /**
     * @param string|null $oldName
     * @return $this
     */
    public function setOldName(?string $oldName): self
    {
        $this->oldName = $oldName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttributeOrder(): ?int
    {
        return $this->attributeOrder;
    }

    /**
     * @param int|null $attributeOrder
     * @return $this
     */
    public function setAttributeOrder(?int $attributeOrder): self
    {
        $this->attributeOrder = $attributeOrder;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getArticleCategoryFieldOptions(): Collection
    {
        return $this->articleCategoryFieldOptions;
    }

    /**
     * @param ArticleCategoryFieldOption $articleCategoryFieldOption
     * @return $this
     */
    public function addArticleCategoryFieldOption(ArticleCategoryFieldOption $articleCategoryFieldOption): self
    {
        if (!$this->articleCategoryFieldOptions->contains($articleCategoryFieldOption)) {
            $this->articleCategoryFieldOptions[] = $articleCategoryFieldOption;
            $articleCategoryFieldOption->setField($this);
        }

        return $this;
    }

    /**
     * @param ArticleCategoryFieldOption $articleCategoryFieldOption
     * @return $this
     */
    public function removeArticleCategoryFieldOption(ArticleCategoryFieldOption $articleCategoryFieldOption): self
    {
        if ($this->articleCategoryFieldOptions->removeElement($articleCategoryFieldOption) && $articleCategoryFieldOption->getField() === $this) {
            $articleCategoryFieldOption->setField(null);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isOptionsField(): bool
    {
        return $this->type === self::ARTICLE_CATEGORY_DROPDOWN_TYPE || $this->type === self::ARTICLE_CATEGORY_CHECKBOX_TYPE;
    }
}
