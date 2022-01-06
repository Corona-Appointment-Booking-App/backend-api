<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\Exception\BookingFinalizeTokenValidationException;

class BookingFinalizeTokenValidator implements BookingFinalizeTokenValidatorInterface
{
    public function validateGeneratedToken(
        string $generatedToken,
        string $providedToken
    ): void {
        if (!hash_equals($generatedToken, $providedToken)) {
            throw new BookingFinalizeTokenValidationException($providedToken, $generatedToken);
        }
    }
}
