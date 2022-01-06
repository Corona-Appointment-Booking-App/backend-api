<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\BookingDto;

interface BookingValidatorInterface
{
    public function validateBooking(BookingDto $bookingDto): void;
}
