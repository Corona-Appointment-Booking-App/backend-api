<?php

declare(strict_types=1);

namespace App\Service\Generator;

interface BookingFinalizeTokenGeneratorInterface
{
    public function generateToken(
        array $participants,
        string $selectedOpeningDayDate,
        string $selectedOpeningTime,
        string $selectedTestCenterId
    ): string;
}
