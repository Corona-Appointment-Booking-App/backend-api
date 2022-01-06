<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\OpeningTime;

interface OpeningTimeServiceInterface
{
    public function getOpeningTimeByUuid(string $uuid): OpeningTime;

    public function getOpeningTimeByTime(\DateTimeImmutable $time): OpeningTime;

    public function getOpeningTimesBetweenFromAndTo(\DateTimeImmutable $from, \DateTimeImmutable $to): array;

    public function getOpeningTimesForDay(string $day, array $openingDays): array;

    public function getOpeningTimes(): array;

    public function createDateTimeFromTime(string $time): \DateTimeImmutable;

    public function createOpeningTime(\DateTimeImmutable $time): OpeningTime;

    public function deleteAllOpeningTimes(): void;
}
