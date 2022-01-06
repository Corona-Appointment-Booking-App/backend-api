<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\CityLocationDto;

interface CityLocationValidatorInterface
{
    public function validateCityLocation(CityLocationDto $cityLocationDto): void;
}
