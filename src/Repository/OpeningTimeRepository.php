<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OpeningTime;

class OpeningTimeRepository extends AbstractRepository
{
    public function getOpeningTimesBetweenFromAndTo(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $openingTimes = $this->createQueryBuilder('o')
            ->where('o.time >= :from')
            ->andWhere('o.time <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('o.time', 'ASC')
            ->getQuery()
            ->getResult();

        if (!\is_array($openingTimes)) {
            return [];
        }

        return $openingTimes;
    }

    public function getOpeningTimeByTime(\DateTimeImmutable $time): ?OpeningTime
    {
        $openingTime = $this->createQueryBuilder('o')
            ->where('o.time = :time')
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$openingTime instanceof OpeningTime) {
            return null;
        }

        return $openingTime;
    }

    protected function getEntityClass(): string
    {
        return OpeningTime::class;
    }
}
