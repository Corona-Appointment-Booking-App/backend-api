<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking implements EntityInterface
{
    public const GROUP_READ = 'booking.read';
    public const GROUP_WRITE = 'booking.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"booking.read", "booking.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TestCenter")
     * @ORM\JoinColumn(name="test_center_id", referencedColumnName="id", nullable=false)
     * @Groups({"booking.read", "booking.write"})
     */
    private TestCenter $testCenter;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"booking.read", "booking.write"})
     */
    private \DateTimeImmutable $time;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     * @Groups({"booking.read", "booking.write"})
     */
    private string $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingParticipant", mappedBy="booking", cascade={"persist"})
     * @Groups({"booking.read", "booking.write"})
     */
    private Collection $participants;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"booking.read", "booking.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"booking.read", "booking.write"})
     */
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->uuid = UuidV4::v4();
        $this->participants = new ArrayCollection();
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

    public function getTestCenter(): TestCenter
    {
        return $this->testCenter;
    }

    public function setTestCenter(TestCenter $testCenter): void
    {
        $this->testCenter = $testCenter;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(\DateTimeImmutable $time): void
    {
        $this->time = $time;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getParticipants()
    {
        return $this->participants;
    }

    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }

    public function addParticipant(BookingParticipant $participant): void
    {
        $this->participants->add($participant);
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
