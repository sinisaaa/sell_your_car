<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleArticleCategoryFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleArticleCategoryFieldRepository::class)
 */
class ArticleArticleCategoryField
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"article_category_field.get"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="categoryFields")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Article $article;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategoryField::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"article_category_field.get"})
     */
    private ArticleCategoryField $field;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"article_category_field.get"})
     */
    private mixed $value;

    /**
     * @ORM\ManyToMany(targetEntity=ArticleCategoryFieldOption::class)
     * @Groups({"article_category_field.get"})
     */
    private Collection $fieldOptions;

    public function __construct()
    {
        $this->fieldOptions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return ArticleCategoryField
     */
    public function getField(): ArticleCategoryField
    {
        return $this->field;
    }

    /**
     * @param ArticleCategoryField $field
     * @return $this
     */
    public function setField(ArticleCategoryField $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param Article $article
     * @param ArticleCategoryField $field
     * @param string|null $value
     * @return ArticleArticleCategoryField
     */
    public static function create(
        Article $article,
        ArticleCategoryField $field,
        ?string $value = null
    ): ArticleArticleCategoryField
    {
        $articleField = new self();
        return $articleField->setArticle($article)
            ->setField($field)
            ->setValue($value);
    }

    /**
     * @return Collection
     */
    public function getFieldOptions(): Collection
    {
        return $this->fieldOptions;
    }

    /**
     * @param ArticleCategoryFieldOption $fieldOption
     * @return $this
     */
    public function addFieldOption(ArticleCategoryFieldOption $fieldOption): self
    {
        if (!$this->fieldOptions->contains($fieldOption)) {
            $this->fieldOptions[] = $fieldOption;
        }

        return $this;
    }

    /**
     * @param ArticleCategoryFieldOption $fieldOption
     * @return $this
     */
    public function removeFieldOption(ArticleCategoryFieldOption $fieldOption): self
    {
        $this->fieldOptions->removeElement($fieldOption);

        return $this;
    }
}
