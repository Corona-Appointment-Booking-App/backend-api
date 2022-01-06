<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataTransferObject\CityLocationDto;
use App\Entity\City;
use App\Service\CityLocationServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityLocationFixtures extends Fixture implements DependentFixtureInterface
{
    public const CITY_LOCATION_REFERENCE = 'cityLocation';

    private CityLocationServiceInterface $cityLocationService;

    public function __construct(CityLocationServiceInterface $cityLocationService)
    {
        $this->cityLocationService = $cityLocationService;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var City $city */
        $city = $this->getReference(CityFixtures::CITY_REFERENCE);

        $cityLocationDto = (new CityLocationDto())->fromArray([
            'cityId' => $city->getUuid()->toRfc4122(),
            'name' => 'Bilk',
        ]);

        $createdCityLocation = $this->cityLocationService->createCityLocation($cityLocationDto);
        $this->addReference(self::CITY_LOCATION_REFERENCE, $createdCityLocation);
    }

    public function getDependencies()
    {
        return [
          CityFixtures::class,
        ];
    }
}
