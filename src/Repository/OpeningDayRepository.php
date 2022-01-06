<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OpeningDay;

class OpeningDayRepository extends AbstractRepository
{
    public function getOpeningDayByDay(string $day): ?OpeningDay
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.day = :day')
            ->setParameter('day', $day)
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected function getEntityClass(): string
    {
        return OpeningDay::class;
    }
}
