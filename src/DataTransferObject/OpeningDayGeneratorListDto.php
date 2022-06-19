<?php

declare(strict_types=1);

namespace App\DataTransferObject;

class OpeningDayGeneratorListDto
{
    use ArrayAssignableTrait;

    private string $id;

    private string $day;

    private string $date;

    private string $openingTimeFrom;

    private string $openingTimeTo;

    private bool $isBookedOut;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function setDay(string $day): void
    {
        $this->day = $day;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getOpeningTimeFrom(): string
    {
        return $this->openingTimeFrom;
    }

    public function setOpeningTimeFrom(string $openingTimeFrom): void
    {
        $this->openingTimeFrom = $openingTimeFrom;
    }

    public function getOpeningTimeTo(): string
    {
        return $this->openingTimeTo;
    }

    public function setOpeningTimeTo(string $openingTimeTo): void
    {
        $this->openingTimeTo = $openingTimeTo;
    }

    public function isBookedOut(): bool
    {
        return $this->isBookedOut;
    }

    public function setIsBookedOut(bool $isBookedOut): void
    {
        $this->isBookedOut = $isBookedOut;
    }
}
