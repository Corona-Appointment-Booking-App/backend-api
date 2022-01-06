<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TestCenter;

class TestCenterRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return TestCenter::class;
    }
}
