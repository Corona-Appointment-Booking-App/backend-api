<?php

declare(strict_types=1);

namespace App\DataTransferObject;

class OpeningDayGeneratorListCollection
{
    use ArrayAssignableTrait;

    /**
     * @var OpeningDayGeneratorListDto[]
     */
    private array $openingDays = [];

    public function getOpeningDays(): array
    {
        return $this->openingDays;
    }

    public function setOpeningDays(array $openingDays): void
    {
        $this->openingDays = $openingDays;
    }

    public function addOpeningDay(OpeningDayGeneratorListDto $openingDayGeneratorListDto): void
    {
        $this->openingDays[] = $openingDayGeneratorListDto;
    }
}
