<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\OpeningTimeDto;

interface OpeningTimeValidatorInterface
{
    public function validateOpeningTime(OpeningTimeDto $openingTimeDto): void;
}
