<?php

declare(strict_types=1);

namespace App\Service\Generator;

use App\DataTransferObject\OpeningDayCollection;
use App\DataTransferObject\OpeningDayDto;

class OpeningDayGeneratorText implements OpeningDayGeneratorTextInterface
{
    public function generateOpeningDaysText(OpeningDayCollection $openingDayCollection): string
    {
        $daysWithSameOpeningTimes = $this->getSameOpeningTimes($openingDayCollection);
        $openingDays = $this->getOpeningDays($openingDayCollection);
        $daysWithDifferentOpeningTimes = array_filter($openingDays, fn (array $openingDay): bool => !\in_array($openingDay, $daysWithSameOpeningTimes, true));

        $firstSameTimeDay = $daysWithSameOpeningTimes[0];
        $lastSameTimeDay = $daysWithSameOpeningTimes[\count($daysWithSameOpeningTimes) - 1];
        $text = $this->getOpeningDaysTextPrefix($daysWithSameOpeningTimes, $daysWithDifferentOpeningTimes, $firstSameTimeDay, $lastSameTimeDay);

        $text = $this->getOpeningDaysWithSameOpeningTimesText($text, $daysWithSameOpeningTimes);
        $text = $this->getOpeningDaysWithDifferentOpeningTimesText($text, $daysWithDifferentOpeningTimes);

        return $text;
    }

    private function getSameOpeningTimes(OpeningDayCollection $openingDayCollection): array
    {
        $openingTimes = $this->getOpeningDays($openingDayCollection);
        $days = [];

        foreach ($openingDayCollection->getOpeningDays() as $openingDayDto) {
            $days[] = $openingDayDto->getDay();
        }

        foreach ($days as $day) {
            foreach ($openingTimes as $openingTime) {
                $sameTimesButNotForCurrentDay = $this->filterSameTimesButNotSameDay($openingTimes, $openingTime['times'], $day);
                $sameTimesForCurrentDay = $this->filterSameTimesButSameDay($openingTimes, $openingTime['times'], $day);

                if ($sameTimesButNotForCurrentDay && $sameTimesForCurrentDay) {
                    return array_merge_recursive($sameTimesForCurrentDay, $sameTimesButNotForCurrentDay);
                }

                if ($sameTimesButNotForCurrentDay && !$sameTimesForCurrentDay) {
                    return $sameTimesButNotForCurrentDay;
                }
            }
        }

        return $this->getSameOpeningTimes($openingDayCollection);
    }

    private function filterSameTimesButNotSameDay(array $openingTimes, array $expectedOpeningTimes, string $excludedDay): array
    {
        $filtered = [];

        foreach ($openingTimes as $openingTime) {
            if ($openingTime['day'] !== $excludedDay && $openingTime['times'] === $expectedOpeningTimes) {
                $filtered[] = $openingTime;
            }
        }

        return $filtered;
    }

    private function filterSameTimesButSameDay(array $openingTimes, array $expectedOpeningTimes, string $expectedDay): array
    {
        $filtered = [];

        foreach ($openingTimes as $openingTime) {
            if ($openingTime['day'] === $expectedDay && $openingTime['times'] === $expectedOpeningTimes) {
                $filtered[] = $openingTime;
            }
        }

        return $filtered;
    }

    private function getOpeningDays(OpeningDayCollection $openingDayCollection): array
    {
        $openingDays = [];

        foreach ($openingDayCollection->getOpeningDays() as $openingDayDto) {
            $times = [];
            foreach ($openingDayDto->getOpeningTimes() as $openingTimeDto) {
                $times[] = [
                    'from' => $openingTimeDto->getFrom(),
                    'to' => $openingTimeDto->getTo(),
                ];
            }

            $openingDays[] = [
                'day' => $this->getMappedDayFromDay($openingDayDto->getDay()),
                'times' => $times,
            ];
        }

        return $openingDays;
    }

    private function getOpeningDaysTextPrefix(array $daysWithSameOpeningTimes, array $daysWithDifferentOpeningTimes, array $firstSameTimeDay, array $lastSameTimeDay): string
    {
        if (\count($daysWithSameOpeningTimes) !== \count($daysWithDifferentOpeningTimes)) {
            return sprintf(
                implode(',', array_column($daysWithSameOpeningTimes, 'day')),
                $firstSameTimeDay['day'],
                $lastSameTimeDay['day']
            );
        }

        return sprintf(
            '%s',
            $firstSameTimeDay['day']
        );
    }

    private function getOpeningDaysWithSameOpeningTimesText(string $text, array $daysWithSameOpeningTimes): string
    {
        $textArray[] = $text;
        $filteredTimesForDaysWithSameOpeningTimes = array_values(array_unique(array_column($daysWithSameOpeningTimes, 'times'), \SORT_ASC))[0];
        foreach ($filteredTimesForDaysWithSameOpeningTimes as $openingTimeKey => $openingTime) {
            $textArray[] = sprintf(' %s-%s Uhr', $openingTime['from'], $openingTime['to']);

            if ($openingTimeKey === \count($filteredTimesForDaysWithSameOpeningTimes) - 1 && \count($filteredTimesForDaysWithSameOpeningTimes) > 1 && \count($daysWithSameOpeningTimes) > 1) {
                $textArray[] = ',';
            }
        }

        return implode('', $textArray);
    }

    private function getOpeningDaysWithDifferentOpeningTimesText(string $text, array $daysWithDifferentOpeningTimes): string
    {
        $textArray[] = $text;

        $filteredTimesForDaysWithDifferentOpeningTimes = array_values(array_unique($daysWithDifferentOpeningTimes, \SORT_ASC));

        foreach ($filteredTimesForDaysWithDifferentOpeningTimes as $openingTimeIndex => $openingTime) {
            $filteredOpeningTimes = array_values(array_unique($openingTime['times'], \SORT_ASC));

            $textArray[] = sprintf('%s', $openingTime['day']);

            foreach ($filteredOpeningTimes as $filteredOpeningTimeIndex => $filteredOpeningTime) {
                $textArray[] = sprintf('%s-%s Uhr', $filteredOpeningTime['from'], $filteredOpeningTime['to']);
            }
        }

        return implode(' ', $textArray);
    }

    private function getMappedDayFromDay(string $day): ?string
    {
        $mappedDays = [
            OpeningDayDto::DAY_MONDAY => 'mo',
            OpeningDayDto::DAY_TUESDAY => 'di',
            OpeningDayDto::DAY_WEDNESDAY => 'mi',
            OpeningDayDto::DAY_THURSDAY => 'do',
            OpeningDayDto::DAY_FRIDAY => 'fr',
            OpeningDayDto::DAY_SATURDAY => 'sa',
            OpeningDayDto::DAY_SUNDAY => 'so',
        ];

        return ucfirst($mappedDays[$day]) ?? null;
    }
}
