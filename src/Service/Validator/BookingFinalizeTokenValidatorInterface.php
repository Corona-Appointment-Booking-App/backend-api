<?php

declare(strict_types=1);

namespace App\Service\Validator;

interface BookingFinalizeTokenValidatorInterface
{
    public function validateGeneratedToken(
        string $generatedToken,
        string $providedToken
    ): void;
}
