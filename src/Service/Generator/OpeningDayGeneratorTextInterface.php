<?php

declare(strict_types=1);

namespace App\Service\Generator;

use App\DataTransferObject\OpeningDayCollection;

interface OpeningDayGeneratorTextInterface
{
    public function generateOpeningDaysText(OpeningDayCollection $openingDayCollection): string;
}
