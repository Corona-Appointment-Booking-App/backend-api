<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Booking;

interface BookingMailServiceInterface
{
    public function sendEmailConfirmation(Booking $booking, string $type): void;
}
