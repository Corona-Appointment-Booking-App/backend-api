<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Booking;
use Symfony\Contracts\EventDispatcher\Event;

class BookingCancelledEvent extends Event
{
    public const NAME = 'booking.cancelled';

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}
