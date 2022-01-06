<?php

declare(strict_types=1);

namespace App\Service\Generator;

interface OpeningTimeGeneratorInterface
{
    public function generateOpeningTimes(): array;
}
