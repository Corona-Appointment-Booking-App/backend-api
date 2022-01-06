<?php

declare(strict_types=1);

namespace App\Service\Validator;

use App\DataTransferObject\CityDto;

interface CityValidatorInterface
{
    public function validateCity(CityDto $cityDto): void;
}
