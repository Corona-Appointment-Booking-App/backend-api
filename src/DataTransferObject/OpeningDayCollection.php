<?php

declare(strict_types=1);

namespace App\DataTransferObject;

class OpeningDayCollection extends AbstractDto
{
    /**
     * @var OpeningDayDto[]
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

    public function addOpeningDay(OpeningDayDto $openingDayDto): void
    {
        $this->openingDays[] = $openingDayDto;
    }

    public function createOpeningDayCollectionFromArray(array $openingDays): self
    {
        $openingDayCollection = new self();

        foreach ($openingDays as $openingDay) {
            $openingDayDto = new OpeningDayDto();
            $openingDayDto->setDay($openingDay['day']);

            foreach ($openingDay['times'] as $openingTime) {
                $openingTimeDto = new OpeningTimeDto();
                $openingTimeDto->setFrom((string) $openingTime['from']);
                $openingTimeDto->setTo((string) $openingTime['to']);
                $openingTimeDto->setOpeningDay($openingDayDto);

                $openingDayDto->addOpeningTime($openingTimeDto);
            }

            $openingDayCollection->addOpeningDay($openingDayDto);
        }

        return $openingDayCollection;
    }
}
