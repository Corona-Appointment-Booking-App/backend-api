<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\CityDto;

class CityHydrator implements CityHydratorInterface
{
    public function hydrateFromArray(array $payload): CityDto
    {
        return (new CityDto())->fromArray([
            'id' => (string) $payload['id'] ??= '',
            'name' => (string) $payload['name'] ??= '',
        ]);
    }
}
