<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OpeningDayRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=OpeningDayRepository::class)
 */
class OpeningDay implements EntityInterface
{
    public const GROUP_READ = 'openingDay.read';
    public const GROUP_WRITE = 'openingDay.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"openingDay.read", "openingDay.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\Column(type="string")
     * @Groups({"openingDay.read", "openingDay.write"})
     */
    private string $day;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"openingDay.read", "openingDay.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"openingDay.read", "openingDay.write"})
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

    public function getDay(): string
    {
        return $this->day;
    }

    public function setDay(string $day): void
    {
        $this->day = $day;
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
