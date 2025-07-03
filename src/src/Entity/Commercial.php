<?php

namespace App\Entity;

use App\Repository\CommercialRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gregwar\Image\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CommercialRepository::class)
 * @Vich\Uploadable
 */
class Commercial
{

    public const POSITION_MAIN = 'main';
    public const POSITION_FEATURED = 'featured';
    public const POSITION_SINGLE = 'single';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"commercial.get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"commercial.get"})
     */
    private string $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $image = null;

    /**
     * @Vich\UploadableField(mapping="commercial_images", fileNameProperty="image")
     *
     * @var File|null
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"commercial.get"})
     */
    private string $position;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"commercial.get"})
     */
    private int $positionNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $shownOn = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return $this
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return Commercial
     */
    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPositionNumber(): ?int
    {
        return $this->positionNumber;
    }

    /**
     * @param int $positionNumber
     * @return $this
     */
    public function setPositionNumber(int $positionNumber): self
    {
        $this->positionNumber = $positionNumber;

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
     * @return Commercial
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
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     * @return Commercial
     */
    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getShownOn(): ?DateTimeInterface
    {
        return $this->shownOn;
    }

    /**
     * @param DateTimeInterface|null $shownOn
     * @return $this
     */
    public function setShownOn(?\DateTimeInterface $shownOn): self
    {
        $this->shownOn = $shownOn;

        return $this;
    }

    /**
     * @Groups({"commercial.get"})
     * @return string|null
     */
    public function getImageThumb(): ?string
    {
        $thumb = null;

        if ($this->imageFile) {
            $thumb = Image::open($this->imageFile->getRealPath())->png();
        }

        return $thumb;
    }

    /**
     * @return string[]
     */
    public static function getValidPositions(): array
    {
        return [self::POSITION_MAIN, self::POSITION_FEATURED, self::POSITION_SINGLE];
    }

}
