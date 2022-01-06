<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;

class CityRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return City::class;
    }
}
