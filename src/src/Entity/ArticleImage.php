<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleImageRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gregwar\Image\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ArticleImageRepository::class)
 * @Vich\Uploadable
 */
class ArticleImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="articleImages")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Article $article;

    /**
     * @Vich\UploadableField(mapping="article_images", fileNameProperty="name")
     *
     * @var File|null
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $width = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $height = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $extension = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $imageOrder = null;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $updatedAt = null;

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     * @return $this
     */
    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     * @return $this
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param string|null $extension
     * @return $this
     */
    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageOrder(): ?int
    {
        return $this->imageOrder;
    }

    /**
     * @param int|null $imageOrder
     * @return $this
     */
    public function setImageOrder(?int $imageOrder): self
    {
        $this->imageOrder = $imageOrder;

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
     * @return ArticleImage
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
     * @return ArticleImage
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return ArticleImage
     */
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    /**
     * @Groups({"article_image.get"})
     * @return string|null
     */
    public function getSmallGalleryImage(): ?string
    {
        $thumb = null;

        if ($this->imageFile) {
            $thumb = Image::open($this->imageFile->getRealPath())->zoomCrop(374, 210)->jpeg();
        }

        return $thumb;
    }

    /**
     * @Groups({"article_image.get"})
     * @return string|null
     */
    public function getImageThumb(): ?string
    {
        $thumb = null;

        if ($this->imageFile) {
            $thumb =     Image::open($this->imageFile->getRealPath())->jpeg();
        }

        return $thumb;
    }

}
