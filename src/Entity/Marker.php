<?php

namespace App\Entity;

use App\Repository\MarkerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Validator\Constraints as CustomAssert;

/**
 * @ORM\Entity(repositoryClass=MarkerRepository::class)
 * @ORM\Table(name="`markers`")
 */
class Marker
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Difficulty::class)
     */
    private $difficulty;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Широта"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Долгота"})
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var string|null Фото локации.
     *
     * @ORM\Column(type="string", nullable=true, options={"comment"="Фото локации"})
     */
    private ?string $photo;

    /**
     * @var File|null Файл фото локации.
     *
     * @Vich\UploadableField(
     *     mapping = "marker_photo",
     *     fileNameProperty = "photo"
     * )
     */
    private ?File $photoFile = null;

    public function _construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->photoFile = null;
        $this->photo = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDifficulty(): ?Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?Difficulty $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude; 
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Возвращает фото локации.
     *
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * Задаёт фото локации.
     *
     * @param string|null $photo Путь до нового фото локации.
     *
     * @return self
     */
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Задаёт файл фото локации.
     *
     * @param File|null $photoFile Новый файл фото локации.
     *
     * @return self
     */
    public function setPhotoFile(?File $photoFile): self
    {
        $this->photoFile = $photoFile;

        if ($photoFile instanceof UploadedFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * Возврашает файл фото локации.
     *
     * @return File|null
     */
    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }



    public static function getEntityConstraints(): array
    {
        return [
            'id' => [
                new Assert\NotBlank(['message' => "ИД не должно быть пустым."]),
                new Assert\Positive(['message' => "ИД должно быть положительным целым числом."])
            ],
            'name' => new Assert\Required([new Assert\NotBlank(), new Assert\Length(['max' => 255])]),
            'latitude' => new Assert\Required([new Assert\NotBlank(), new Assert\Length(['max' => 255])]),
            'longitudeme' => new Assert\Required([new Assert\NotBlank(), new Assert\Length(['max' => 255])]),

            'description' =>  new Assert\Optional([new Assert\Length(['max' => 3000])]),
            
            'photoFile' =>  new Assert\Optional([
                new Assert\File([
                    'maxSize' => '5120k',
                    'maxSizeMessage' => "Максимально допустимый размер файла равен {{ limit }}."
                ]),
                new CustomAssert\FileMimeTypeConstraint(['allowedMimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ]])
            ])
        ];
    }

    public static function getEntityAttributes(): array
    {
        return [
            'attributes' => [
                'id',
                'name',
                'description',
                'difficulty'=> ['id', 'name'],
                'latitude',
                'longitude',
                'createdBy' => [
                    'id',
                    'username',
                    'fio',
                ],
                'createdAt',
            ]
        ];
    }
}
