<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OpeningTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Table(indexes={
    @ORM\Index(name="opening_time_search_index_uuid", columns={"uuid"}),
    @ORM\Index(name="opening_time_search_index_time", columns={"time"})
   }),
 * @ORM\Entity(repositoryClass=OpeningTimeRepository::class)
 */
class OpeningTime implements EntityInterface
{
    public const GROUP_READ = 'openingTime.read';
    public const GROUP_WRITE = 'openingTime.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"openingTime.read", "openingTime.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\Column(type="datetime_immutable", unique=true)
     * @Groups({"openingTime.read", "openingTime.write"})
     */
    private \DateTimeImmutable $time;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"openingTime.read", "openingTime.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"openingTime.read", "openingTime.write"})
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

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(\DateTimeImmutable $time): void
    {
        $this->time = $time;
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
