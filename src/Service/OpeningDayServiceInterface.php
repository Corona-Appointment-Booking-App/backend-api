<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\OpeningDay;

interface OpeningDayServiceInterface
{
    public function getOpeningDayByUuid(string $uuid): OpeningDay;

    public function getOpeningDayByDay(string $day): OpeningDay;

    public function getOpeningDays(): array;

    public function createOpeningDay(string $day): OpeningDay;

    public function deleteAllOpeningDays(): void;
}
