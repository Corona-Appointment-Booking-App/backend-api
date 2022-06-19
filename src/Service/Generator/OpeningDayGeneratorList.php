<?php

declare(strict_types=1);

namespace App\Service\Generator;

use App\AppConstants;
use App\DataTransferObject\OpeningDayCollection;
use App\DataTransferObject\OpeningDayDto;
use App\DataTransferObject\OpeningDayGeneratorListCollection;
use App\DataTransferObject\OpeningDayGeneratorListDto;
use App\DataTransferObject\OpeningTimeDto;
use App\Exception\OpeningDayNotFoundException;
use App\Service\OpeningDayServiceInterface;

class OpeningDayGeneratorList implements OpeningDayGeneratorListInterface
{
    private const RANGE_FROM = 0;
    private const RANGE_TO = 8;

    private OpeningDayServiceInterface $openingDayService;

    public function __construct(OpeningDayServiceInterface $openingDayService)
    {
        $this->openingDayService = $openingDayService;
    }

    public function generateOpeningDaysForDays(OpeningDayCollection $openingDayCollection, string $date): OpeningDayGeneratorListCollection
    {
        $availableDays = [];
        foreach ($openingDayCollection->getOpeningDays() as $openingDayDto) {
            $availableDays[] = $openingDayDto->getDay();
        }

        $openingDayGeneratorListCollection = new OpeningDayGeneratorListCollection();

        for ($i = self::RANGE_FROM; $i <= self::RANGE_TO; ++$i) {
            $day = (new \DateTimeImmutable(sprintf('%s +%s day', $date, $i)));
            $dayName = mb_strtolower($day->format(AppConstants::FORMAT_DAY));

            if (!\in_array($dayName, $availableDays, true)) {
                continue;
            }

            /** @var OpeningDayDto|null $matchingDay */
            $matchingDay = $this->getMatchingDayFromOpeningDay($dayName, $openingDayCollection);

            if (null === $matchingDay) {
                continue;
            }

            /** @var OpeningTimeDto $lowestOpeningTime */
            $lowestOpeningTime = min($matchingDay->getOpeningTimes());
            $highestOpeningTime = max($matchingDay->getOpeningTimes());

            try {
                $openingDay = $this->openingDayService->getOpeningDayByDay($matchingDay->getDay());

                $listDto = new OpeningDayGeneratorListDto();
                $listDto->setId($openingDay->getUuid()->toRfc4122());
                $listDto->setDay(mb_strtolower($day->format(AppConstants::FORMAT_DAY)));
                $listDto->setDate($day->format(AppConstants::FORMAT_DATE));
                $listDto->setOpeningTimeFrom($lowestOpeningTime->getFrom());
                $listDto->setOpeningTimeTo($highestOpeningTime->getTo());
                $listDto->setIsBookedOut(false);

                $openingDayGeneratorListCollection->addOpeningDay($listDto);
            } catch (OpeningDayNotFoundException $exception) {
                continue;
            }
        }

        return $openingDayGeneratorListCollection;
    }

    public function generateOpeningDaysForDate(OpeningDayCollection $openingDayCollection, string $date): ?OpeningDayGeneratorListDto
    {
        $openingDayGeneratorListCollection = $this->generateOpeningDaysForDays($openingDayCollection, $date);
        $matchingDay = $this->getMatchingDayFromDate($date, $openingDayGeneratorListCollection);

        if (null === $matchingDay) {
            return null;
        }

        return $matchingDay;
    }

    private function getMatchingDayFromOpeningDay(string $dayName, OpeningDayCollection $openingDayCollection): ?OpeningDayDto
    {
        foreach ($openingDayCollection->getOpeningDays() as $openingDayDto) {
            if ($openingDayDto->getDay() === $dayName) {
                return $openingDayDto;
            }
        }

        return null;
    }

    private function getMatchingDayFromDate(string $date, OpeningDayGeneratorListCollection $collection): ?OpeningDayGeneratorListDto
    {
        foreach ($collection->getOpeningDays() as $openingDayDto) {
            if ($openingDayDto->getDate() === $date) {
                return $openingDayDto;
            }
        }

        return null;
    }
}
