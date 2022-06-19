<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Validator;

use App\Exception\BookingFinalizeTokenValidationException;
use App\Service\Validator\BookingFinalizeTokenValidator;
use App\Service\Validator\BookingFinalizeTokenValidatorInterface;
use PHPUnit\Framework\TestCase;

class BookingFinalizeTokenValidatorTest extends TestCase
{
    private BookingFinalizeTokenValidatorInterface $bookingFinalizeTokenValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookingFinalizeTokenValidator = new BookingFinalizeTokenValidator();
    }

    public function testValidateGeneratedTokenThrowsNoExceptionWhenTokenIsValid(): void
    {
        static::expectNotToPerformAssertions();

        $this->bookingFinalizeTokenValidator->validateGeneratedToken(
            '34d09ee48caf81dace25855ac47ea752964f6eaea30f6c85232f915ea5bdba48',
            '34d09ee48caf81dace25855ac47ea752964f6eaea30f6c85232f915ea5bdba48'
        );
    }

    public function testValidateGeneratedTokenThrowsExceptionWhenTokenIsInvalid(): void
    {
        static::expectException(BookingFinalizeTokenValidationException::class);

        $this->bookingFinalizeTokenValidator->validateGeneratedToken(
            '34d09ee48caf81dace25855ac47ea752964f6eaea30f6c85232f915ea5bdba48',
            'f7443f796e5dcdf6f99cf47b20840f60ae49363395ec09d1b20102047468c2ce'
        );
    }
}
