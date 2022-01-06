<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\CityDto;

interface CityHydratorInterface
{
    public function hydrateFromArray(array $payload): CityDto;
}
