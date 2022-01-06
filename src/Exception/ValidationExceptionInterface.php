<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationExceptionInterface
{
    public function getViolationList(): ConstraintViolationListInterface;
}
