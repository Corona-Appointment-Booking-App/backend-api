<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\TestCenterDto;

interface TestCenterValidatorInterface
{
    public function validateTestCenter(TestCenterDto $testCenterDto): void;
}
