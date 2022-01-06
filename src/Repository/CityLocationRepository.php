<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CityLocation;

class CityLocationRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return CityLocation::class;
    }
}
