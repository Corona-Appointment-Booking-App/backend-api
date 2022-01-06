<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\BookingDto;

interface BookingHydratorInterface
{
    public function hydrateFromArray(array $payload): BookingDto;
}
