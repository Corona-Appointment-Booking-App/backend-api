<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataTransferObject\CityDto;
use App\Service\CityServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public const CITY_REFERENCE = 'city';

    private CityServiceInterface $cityService;

    public function __construct(CityServiceInterface $cityService)
    {
        $this->cityService = $cityService;
    }

    public function load(ObjectManager $manager): void
    {
        $cityDto = (new CityDto())->fromArray([
           'name' => 'Düsseldorf',
        ]);

        $createdCity = $this->cityService->createCity($cityDto);
        $this->addReference(self::CITY_REFERENCE, $createdCity);
    }
}
