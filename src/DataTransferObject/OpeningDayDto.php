<?php

declare(strict_types=1);

namespace App\DataTransferObject;

class OpeningDayDto
{
    use ArrayAssignableTrait;

    public const DAY_MONDAY = 'monday';
    public const DAY_TUESDAY = 'tuesday';
    public const DAY_WEDNESDAY = 'wednesday';
    public const DAY_THURSDAY = 'thursday';
    public const DAY_FRIDAY = 'friday';
    public const DAY_SATURDAY = 'saturday';
    public const DAY_SUNDAY = 'sunday';

    private string $day;

    /**
     * @var OpeningTimeDto[]
     */
    private array $openingTimes;

    public function getDay(): string
    {
        return $this->day;
    }

    public function setDay(string $day): void
    {
        $this->day = $day;
    }

    public function getOpeningTimes(): array
    {
        return $this->openingTimes;
    }

    public function setOpeningTimes(array $openingTimes): void
    {
        $this->openingTimes = $openingTimes;
    }

    public function addOpeningTime(OpeningTimeDto $openingTimeDto): void
    {
        $this->openingTimes[] = $openingTimeDto;
    }
}
