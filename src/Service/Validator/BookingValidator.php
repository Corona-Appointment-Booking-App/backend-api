<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\BookingDto;
use App\Exception\BookingValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookingValidator implements BookingValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateBooking(BookingDto $bookingDto): void
    {
        $violations = $this->validator->validate($bookingDto);

        if ($violations->count()) {
            throw new BookingValidationException($violations);
        }
    }
}
