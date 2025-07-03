<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleCategoryFieldOptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleCategoryFieldOptionRepository::class)
 */
class ArticleCategoryFieldOption
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"article_category_field_options.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_category_field_options.get"})
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategoryField::class, inversedBy="articleCategoryFieldOptions")
     */
    private ArticleCategoryField $field;

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
}
