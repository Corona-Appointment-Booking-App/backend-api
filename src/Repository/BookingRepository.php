<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\TestCenter;

class BookingRepository extends AbstractRepository
{
    public function getBookingByTestCenterAndTime(TestCenter $testCenter, \DateTimeImmutable $time): ?Booking
    {
        $booking = $this->createQueryBuilder('b')
            ->where('b.testCenter = :testCenter')
            ->andWhere('b.time = :time')
            ->setParameter('testCenter', $testCenter)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$booking instanceof Booking) {
            return null;
        }

        return $booking;
    }

    protected function getEntityClass(): string
    {
        return Booking::class;
    }
}
