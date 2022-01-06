<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\CityDto;
use App\Exception\CityValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CityValidator implements CityValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateCity(CityDto $cityDto): void
    {
        $violations = $this->validator->validate($cityDto);

        if ($violations->count()) {
            throw new CityValidationException($violations);
        }
    }
}
