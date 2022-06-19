<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CityLocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=CityLocationRepository::class)
 */
class CityLocation implements EntityInterface
{
    public const GROUP_READ = 'cityLocation.read';
    public const GROUP_WRITE = 'cityLocation.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"cityLocation.read", "cityLocation.write", "city.read", "city.write", "testCenter.read", "testCenter.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="locations")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=false)
     * @Groups({"cityLocation.read", "cityLocation.write"})
     */
    private City $city;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TestCenter", mappedBy="cityLocation")
     * @Groups({"cityLocation.read", "cityLocation.write", "city.read", "city.write"})
     */
    private Collection $testCenters;

    /**
     * @ORM\Column(type="string")
     * @Groups({"cityLocation.read", "cityLocation.write", "city.read", "city.write", "testCenter.read", "testCenter.write"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     * @Groups({"cityLocation.read", "cityLocation.write", "city.read", "city.write", "testCenter.read", "testCenter.write"})
     */
    private string $seoSlug;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"cityLocation.read", "cityLocation.write", "city.read", "city.write", "testCenter.read", "testCenter.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"cityLocation.read", "cityLocation.write", "city.read", "city.write", "testCenter.read", "testCenter.write"})
     */
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->uuid = UuidV4::v4();
        $this->testCenters = new ArrayCollection();
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

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
    }

    public function getTestCenters(): Collection
    {
        return $this->testCenters;
    }

    public function setTestCenters(Collection $testCenters): void
    {
        $this->testCenters = $testCenters;
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
