<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OpeningTime;

class OpeningTimeRepository extends AbstractRepository
{
    public function getOpeningTimesBetweenFromAndTo(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.time >= :from')
            ->andWhere('o.time <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('o.time', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getOpeningTimeByTime(\DateTimeImmutable $time): ?OpeningTime
    {
        return $this->createQueryBuilder('o')
            ->where('o.time = :time')
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected function getEntityClass(): string
    {
        return OpeningTime::class;
    }
}
