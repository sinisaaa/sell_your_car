<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gregwar\Image\Image;
use http\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"article.get"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article.get"})
     */
    private string $title;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"article.get"})
     */
    private ?bool $exchange = false;

    /**
     * @ORM\Column(type="decimal", nullable=true, precision=20, scale=2)
     * @Groups({"article.get"})
     */
    private ?float $price = null;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $urgent = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $fixed = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $negotiable = false;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     * @Groups({"article.get"})
     */
    private bool $featuredPending = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"article.get"})
     */
    private ?string $conditions = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"article.get"})
     */
    private ?string $telephone = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"article.get"})
     */
    private bool $discontinued = false;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @Groups({"article.get"})
     */
    private bool $status = true;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"article.get"})
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"article.get"})
     */
    private ?Location $location = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"article_user.get"})
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"article.get"})
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article.get"})
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article.get"})
     */
    private ?DateTimeInterface $soldAt = null;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"article.get"})
     */
    private int $hits = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article.get"})
     */
    private ?DateTimeInterface $featuredFrom = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"article.get"})
     */
    private ?DateTimeInterface $featuredTo = null;

    /**
     * @ORM\OneToMany(targetEntity=ArticleImage::class, mappedBy="article")
     * @Groups({"article_image.get"})
     */
    private Collection $articleImages;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleManufacturer::class)
     * @Groups({"article.get"})
     */
    private ?ArticleManufacturer $manufacturer = null;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleManufacturerModel::class)
     * @Groups({"article.get"})
     */
    private ?ArticleManufacturerModel $manufacturerModel = null;

    /**
     * @ORM\OneToMany(targetEntity=ArticleArticleCategoryField::class, mappedBy="article", orphanRemoval=true)
     * @Groups({"article_category_fields.get"})
     */
    private Collection $categoryFields;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ArticleCategory $category;

    /**
     * @ORM\OneToMany(targetEntity=UserFavoriteArticles::class, mappedBy="article")
     */
    private Collection $favoriteByUsers;

    /**
     * @Groups({"article.get"})
     */
    private bool $favorite = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $pikId = null;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $isDraft = false;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->articleImages = new ArrayCollection();
        $this->categoryFields = new ArrayCollection();
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
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    /**
     * @param string|null $conditions
     * @return $this
     */
    public function setConditions(?string $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return $this
     */
    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFeaturedPending(): ?bool
    {
        return $this->featuredPending;
    }

    /**
     * @param bool $featuredPending
     * @return $this
     */
    public function setFeaturedPending(bool $featuredPending): self
    {
        $this->featuredPending = $featuredPending;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getExchange(): ?bool
    {
        return $this->exchange;
    }

    /**
     * @param bool|null $exchange
     * @return $this
     */
    public function setExchange(?bool $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getUrgent(): ?bool
    {
        return $this->urgent;
    }

    /**
     * @param bool $urgent
     * @return $this
     */
    public function setUrgent(bool $urgent): self
    {
        $this->urgent = $urgent;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDiscontinued(): ?bool
    {
        return $this->discontinued;
    }

    /**
     * @param bool $discontinued
     * @return $this
     */
    public function setDiscontinued(bool $discontinued): self
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNegotiable(): ?bool
    {
        return $this->negotiable;
    }

    /**
     * @param bool $negotiable
     * @return $this
     */
    public function setNegotiable(bool $negotiable): self
    {
        $this->negotiable = $negotiable;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFixed(): ?bool
    {
        return $this->fixed;
    }

    /**
     * @param bool $fixed
     * @return $this
     */
    public function setFixed(bool $fixed): self
    {
        $this->fixed = $fixed;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     * @return $this
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

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
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getSoldAt(): ?DateTimeInterface
    {
        return $this->soldAt;
    }

    /**
     * @param DateTimeInterface|null $soldAt
     * @return $this
     */
    public function setSoldAt(?DateTimeInterface $soldAt): self
    {
        $this->soldAt = $soldAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return $this
     */
    public function setHits(int $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Increments hits counter
     */
    public function incrementHitsCounter(): void
    {
        $this->hits++;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFeaturedFrom(): ?\DateTimeInterface
    {
        return $this->featuredFrom;
    }

    /**
     * @param DateTimeInterface|null $featuredFrom
     * @return $this
     */
    public function setFeaturedFrom(?\DateTimeInterface $featuredFrom): self
    {
        $this->featuredFrom = $featuredFrom;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFeaturedTo(): ?DateTimeInterface
    {
        return $this->featuredTo;
    }

    /**
     * @param DateTimeInterface|null $featuredTo
     * @return $this
     */
    public function setFeaturedTo(?DateTimeInterface $featuredTo): self
    {
        $this->featuredTo = $featuredTo;

        return $this;
    }

    /**
     * @return bool
     * @Groups({"article.get"})
     */
    public function isFeatured(): bool
    {
        return null !== $this->featuredFrom && null !== $this->featuredTo && $this->featuredFrom <= new DateTime() && $this->featuredTo >= new DateTime();
    }

    /**
     * @return int[]
     */
    public static function getValidFeaturedPeriods(): array
    {
        return [1, 3, 5, 10];
    }

    /**
     * @return int[]
     */
    public static function getPeriodPrices(): array
    {
        return [1 => 20, 3 => 40, 5 => 60, 10 => 100];
    }

    /**
     * @param int $period
     *
     * @return int
     */
    public static function getPeriodFeaturedPrice(int $period): int
    {
        if (false === array_key_exists($period, self::getPeriodPrices())) {
            throw new InvalidArgumentException('Invalid feature period');
        }

        return self::getPeriodPrices()[$period];
    }

    /**
     * @return Collection<int, ArticleImage>
     */
    public function getArticleImages(): Collection
    {
        return $this->articleImages;
    }

    /**
     * @param ArticleImage $articleImage
     * @return $this
     */
    public function addArticleImage(ArticleImage $articleImage): self
    {
        if (!$this->articleImages->contains($articleImage)) {
            $this->articleImages[] = $articleImage;
            $articleImage->setArticle($this);
        }

        return $this;
    }

    /**
     * @param ArticleImage $articleImage
     * @return $this
     */
    public function removeArticleImage(ArticleImage $articleImage): self
    {
        if ($this->articleImages->removeElement($articleImage) && $articleImage->getArticle() === $this) {
            $articleImage->setArticle(null);
        }

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"article.get"})
     */
    public function getImageThumb(): ?string
    {
        if (0 < $this->getArticleImages()->count()) {
            /** @var ArticleImage $firstImage */
            $firstImage = $this->getArticleImages()->first();
            return $firstImage->getSmallGalleryImage();
        }

        return null;
    }

    /**
     * @return ArticleManufacturer|null
     */
    public function getManufacturer(): ?ArticleManufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param ArticleManufacturer|null $manufacturer
     * @return $this
     */
    public function setManufacturer(?ArticleManufacturer $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return ArticleManufacturerModel|null
     */
    public function getManufacturerModel(): ?ArticleManufacturerModel
    {
        return $this->manufacturerModel;
    }

    /**
     * @param ArticleManufacturerModel|null $manufacturerModel
     * @return $this
     */
    public function setManufacturerModel(?ArticleManufacturerModel $manufacturerModel): self
    {
        $this->manufacturerModel = $manufacturerModel;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategoryFields(): Collection
    {
        return $this->categoryFields;
    }

    /**
     * @param ArticleArticleCategoryField $categoryField
     * @return $this
     */
    public function addCategoryField(ArticleArticleCategoryField $categoryField): self
    {
        if (!$this->categoryFields->contains($categoryField)) {
            $this->categoryFields[] = $categoryField;
            $categoryField->setArticle($this);
        }

        return $this;
    }

    /**
     * @param ArticleArticleCategoryField $categoryField
     * @return $this
     */
    public function removeCategoryField(ArticleArticleCategoryField $categoryField): self
    {
        if ($this->categoryFields->removeElement($categoryField) && $categoryField->getArticle() === $this) {
            $categoryField->setArticle(null);
        }

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
     * @return Collection
     */
    public function getFavoriteByUsers(): Collection
    {
        return $this->favoriteByUsers;
    }

    /**
     * @return bool
     */
    public function isFavorite(): bool
    {
        return $this->favorite;
    }

    /**
     * @param bool $favorite
     * @return Article
     */
    public function setFavorite(bool $favorite): self
    {
        $this->favorite = $favorite;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPikId(): ?int
    {
        return $this->pikId;
    }

    /**
     * @param int|null $pikId
     * @return $this
     */
    public function setPikId(?int $pikId): self
    {
        $this->pikId = $pikId;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"article.get"})
     */
    public function getMileage(): ?string
    {
        /** @var ArticleArticleCategoryField $articleCategoryField */
        foreach($this->getCategoryFields() as $articleCategoryField) {
            if ('mileage' === $articleCategoryField->getField()->getOldName()) {
                return $articleCategoryField->getValue();
            }
        }

        return '';
    }

    /**
     * @return string|null
     * @Groups({"article.get"})
     */
    public function getFuel(): ?string
    {
        /** @var ArticleArticleCategoryField $articleCategoryField */
        foreach($this->getCategoryFields() as $articleCategoryField) {
            if ('fuel' === $articleCategoryField->getField()->getOldName()) {
                return $articleCategoryField->getFieldOptions()->first() ? $articleCategoryField->getFieldOptions()->first()->getName() : '';
            }
        }

        return '';
    }

    /**
     * @return string|null
     * @Groups({"article.get"})
     */
    public function getProductionYear(): ?string
    {
        /** @var ArticleArticleCategoryField $articleCategoryField */
        foreach($this->getCategoryFields() as $articleCategoryField) {
            if ('prod_year' === $articleCategoryField->getField()->getOldName()) {
                return $articleCategoryField->getValue();
            }
        }

        return '';
    }

    /**
     * @return bool
     */
    public function getIsDraft(): bool
    {
        return $this->isDraft;
    }

    /**
     * @param bool $isDraft
     * @return $this
     */
    public function setIsDraft(bool $isDraft): self
    {
        $this->isDraft = $isDraft;

        return $this;
    }

}
