<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\BookingDto;
use App\Entity\Booking;
use App\Entity\TestCenter;
use App\Repository\Result\PaginatedItemsResult;

interface BookingServiceInterface
{
    public function getBookingByUuid(string $uuid): Booking;

    public function getBookingByTestCenterAndTime(TestCenter $testCenter, \DateTimeImmutable $time): Booking;

    public function getRecentBookingsWithPagination(int $page, int $bookingsPerPage): PaginatedItemsResult;

    public function getTotalBookingsCount(bool $onlyFromToday = false): int;

    public function createBooking(BookingDto $bookingDto): Booking;
}
