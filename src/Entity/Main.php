<?php

namespace App\Entity;

use App\Repository\MainRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MainRepository::class)]
class Main
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $main_image = null;

    #[Assert\Image(mimeTypes: ['image/webp', 'image/jpeg', 'image/png'])]
    #[Vich\UploadableField(mapping: 'main', fileNameProperty: 'main_image')]
    private UploadedFile|File|null $main_image_imageFile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->main_image;
    }

    public function setMainImage(?string $main_image): static
    {
        $this->main_image = $main_image;

        return $this;
    }

    public function getMainImageImageFile(): File|UploadedFile|null
    {
        return $this->main_image_imageFile;
    }

    public function setMainImageImageFile(File|UploadedFile|null $main_image_imageFile): static
    {
        $this->main_image_imageFile = $main_image_imageFile;

        if (null !== $main_image_imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }
}
