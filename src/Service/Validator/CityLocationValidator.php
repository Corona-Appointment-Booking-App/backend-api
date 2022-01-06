<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\CityLocationDto;
use App\Exception\CityLocationValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CityLocationValidator implements CityLocationValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateCityLocation(CityLocationDto $cityLocationDto): void
    {
        $violations = $this->validator->validate($cityLocationDto);

        if ($violations->count()) {
            throw new CityLocationValidationException($violations);
        }
    }
}
