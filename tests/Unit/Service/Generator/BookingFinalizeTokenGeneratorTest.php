<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Generator;

use App\Exception\BookingFinalizeTokenGeneratorException;
use App\Service\Generator\BookingFinalizeTokenGenerator;
use PHPUnit\Framework\TestCase;

class BookingFinalizeTokenGeneratorTest extends TestCase
{
    public function testGenerateToken(): void
    {
        $bookingFinalizeTokenGenerator = new BookingFinalizeTokenGenerator('testAppSecret');
        $participants = [
            [
                'firstName' => 'testFirstName',
                'lastName' => 'testLastName',
                'zipCode' => '012345',
                'city' => 'testCity',
                'birthDate' => '01.01.1970',
                'phoneNumber' => '+49123456789',
                'street' => 'testStreet',
                'houseNumber' => '5',
                'email' => 'test@email.com',
            ]
        ];
        $generatedToken = $bookingFinalizeTokenGenerator->generateToken(
            $participants,
            '03.12.2021',
            '20:10',
            '9c5807d8-8712-4f36-8370-a3fbd2946f83'
        );

        static::assertSame('34d09ee48caf81dace25855ac47ea752964f6eaea30f6c85232f915ea5bdba48', $generatedToken);
    }

    public function testGenerateTokenThrowsExceptionWhenAppSecretIsMissing(): void
    {
        static::expectException(BookingFinalizeTokenGeneratorException::class);

        $bookingFinalizeTokenGenerator = new BookingFinalizeTokenGenerator('');
        $bookingFinalizeTokenGenerator->generateToken(
            [],
            '',
            '',
            ''
        );
    }
}
