<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\CityLocationDto;

class CityLocationHydrator implements CityLocationHydratorInterface
{
    public function hydrateFromArray(array $payload): CityLocationDto
    {
        return (new CityLocationDto())->fromArray([
            'id' => (string) $payload['id'] ??= '',
            'cityId' => (string) $payload['cityId'] ??= '',
            'name' => (string) $payload['name'] ??= '',
        ]);
    }
}
