<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookingParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass=BookingParticipantRepository::class)
 */
class BookingParticipant implements EntityInterface
{
    public const GROUP_READ = 'bookingParticipant.read';
    public const GROUP_WRITE = 'bookingParticipant.write';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid")
     * @SerializedName("id")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private AbstractUid $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="participants")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id", nullable=false)
     */
    private Booking $booking;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $lastName;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private \DateTimeImmutable $birthDate;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $street;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $houseNumber;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $zipCode;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $city;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $phoneNumber;

    /**
     * @ORM\Column(type="string")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private string $email;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"bookingParticipant.read", "bookingParticipant.write", "booking.read", "booking.write"})
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

    public function getBooking(): Booking
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking): void
    {
        $this->booking = $booking;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
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
