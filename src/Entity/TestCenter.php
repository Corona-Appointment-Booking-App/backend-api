<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TestCenterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=TestCenterRepository::class)
 */
class TestCenter implements EntityInterface
{
    public const GROUP_READ = 'testCenter.read';
    public const GROUP_WRITE = 'testCenter.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write", "booking.read", "booking.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CityLocation", inversedBy="testCenters")
     * @ORM\JoinColumn(name="city_location_id", referencedColumnName="id", nullable=false)
     * @Groups({"testCenter.read", "testCenter.write"})
     */
    private CityLocation $cityLocation;

    /**
     * @ORM\Column(type="string")
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write", "booking.read", "booking.write"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private string $address;

    /**
     * @ORM\Column(type="string")
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private string $seoSlug;

    /**
     * @ORM\Column(type="json")
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private array $openingDays;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"testCenter.read", "testCenter.write", "city.read", "city.write", "cityLocation.read", "cityLocation.write"})
     */
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->uuid = UuidV4::v4();
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

    public function getCityLocation(): CityLocation
    {
        return $this->cityLocation;
    }

    public function setCityLocation(CityLocation $cityLocation): void
    {
        $this->cityLocation = $cityLocation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getSeoSlug(): string
    {
        return $this->seoSlug;
    }

    public function setSeoSlug(string $seoSlug): void
    {
        $this->seoSlug = $seoSlug;
    }

    public function getOpeningDays(): array
    {
        return $this->openingDays;
    }

    public function setOpeningDays(array $openingDays): void
    {
        $this->openingDays = $openingDays;
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
