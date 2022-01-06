<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\CityLocationDto;

interface CityLocationHydratorInterface
{
    public function hydrateFromArray(array $payload): CityLocationDto;
}
