<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\OpeningTimeDto;
use App\Exception\OpeningTimeValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OpeningTimeValidator implements OpeningTimeValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateOpeningTime(OpeningTimeDto $openingTimeDto): void
    {
        $violations = $this->validator->validate($openingTimeDto);

        if ($violations->count()) {
            throw new OpeningTimeValidationException($violations);
        }
    }
}
