<?php

declare(strict_types=1);

namespace App\Service\Generator;

use App\DataTransferObject\OpeningDayCollection;
use App\DataTransferObject\OpeningDayGeneratorListCollection;
use App\DataTransferObject\OpeningDayGeneratorListDto;

interface OpeningDayGeneratorListInterface
{
    public function generateOpeningDaysForDays(OpeningDayCollection $openingDayCollection, string $date): OpeningDayGeneratorListCollection;

    public function generateOpeningDaysForDate(OpeningDayCollection $openingDayCollection, string $date): ?OpeningDayGeneratorListDto;
}
