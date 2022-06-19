<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City implements EntityInterface
{
    public const GROUP_READ = 'city.read';
    public const GROUP_WRITE = 'city.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CityLocation", mappedBy="city")
     * @Groups({"city.read", "city.write"})
     */
    private Collection $locations;

    /**
     * @ORM\Column(type="string")
     * @Groups({"city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private string $seoSlug;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->uuid = UuidV4::v4();
        $this->locations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): AbstractUid
    {
        return $this->uuid;
    }

    public function setUuid(AbstractUid $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function setLocations(Collection $locations): void
    {
        $this->locations = $locations;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSeoSlug(): string
    {
        return $this->seoSlug;
    }

    public function setSeoSlug(string $seoSlug): void
    {
        $this->seoSlug = $seoSlug;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getSerializationGroups(): array
    {
        return [static::GROUP_READ, static::GROUP_WRITE];
    }
}
