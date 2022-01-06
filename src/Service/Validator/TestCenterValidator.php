<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\TestCenterDto;
use App\Exception\CityValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestCenterValidator implements TestCenterValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateTestCenter(TestCenterDto $testCenterDto): void
    {
        $violations = $this->validator->validate($testCenterDto);

        if ($violations->count()) {
            throw new CityValidationException($violations);
        }
    }
}
