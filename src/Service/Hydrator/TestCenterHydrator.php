<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\DataTransferObject\TestCenterDto;

class TestCenterHydrator implements TestCenterHydratorInterface
{
    public function hydrateFromArray(array $payload): TestCenterDto
    {
        return (new TestCenterDto())->fromArray([
            'id' => (string) $payload['id'] ??= '',
            'cityLocationId' => (string) $payload['cityLocationId'] ??= '',
            'name' => (string) $payload['name'] ??= '',
            'address' => (string) $payload['address'] ??= '',
            'openingDays' => (array) $payload['openingDays'] ??= [],
        ]);
    }
}
