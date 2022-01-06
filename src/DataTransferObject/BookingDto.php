<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class BookingDto extends AbstractDto
{
    /**
     * @Assert\NotBlank
     */
    private string $testCenterId;

    /**
     * @Assert\All({
     *    @Assert\Type("App\DataTransferObject\BookingParticipantDto")
     * })
     * @Assert\Count(
     *      min = 1,
     *      max = 5,
     *      minMessage = "No participants specified",
     *      maxMessage = "Only {{ limit }} participants allowed"
     * )
     * @Assert\Valid
     */
    private iterable $participants = [];

    /**
     * @Assert\NotBlank
     */
    private \DateTimeImmutable $bookingTime;

    public function getTestCenterId(): string
    {
        return $this->testCenterId;
    }

    public function setTestCenterId(string $testCenterId): void
    {
        $this->testCenterId = $testCenterId;
    }

    /**
     * @return iterable|BookingParticipantDto[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function addParticipant(BookingParticipantDto $participantDto): void
    {
        $this->participants[] = $participantDto;
    }

    public function getBookingTime(): \DateTimeImmutable
    {
        return $this->bookingTime;
    }

    public function setBookingTime(\DateTimeImmutable $bookingTime): void
    {
        $this->bookingTime = $bookingTime;
    }
}
