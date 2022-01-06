<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataTransferObject\OpeningDayDto;
use App\DataTransferObject\TestCenterDto;
use App\Entity\CityLocation;
use App\Service\TestCenterServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TestCenterFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEST_CENTER_REFERENCE = 'testCenter';

    private TestCenterServiceInterface $testCenterService;

    public function __construct(TestCenterServiceInterface $testCenterService)
    {
        $this->testCenterService = $testCenterService;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var CityLocation $cityLocation */
        $cityLocation = $this->getReference(CityLocationFixtures::CITY_LOCATION_REFERENCE);

        $testCenterDto = (new TestCenterDto())->fromArray([
            'cityLocationId' => $cityLocation->getUuid()->toRfc4122(),
            'name' => 'Test Center Düsseldorf Arcaden',
            'address' => 'Friedrichstraße 133, 40217 Düsseldorf',
            'openingDays' => $this->getOpeningDays(),
        ]);

        $createdTestCenter = $this->testCenterService->createTestCenter($testCenterDto);
        $this->setReference(self::TEST_CENTER_REFERENCE, $createdTestCenter);
    }

    private function getOpeningDays(): array
    {
        return [
            [
                'day' => OpeningDayDto::DAY_MONDAY,
                'times' => [
                    [
                        'from' => '09:00',
                        'to' => '11:00',
                    ],
                    [
                        'from' => '16:00',
                        'to' => '18:00',
                    ],
                ],
            ],
            [
                'day' => OpeningDayDto::DAY_TUESDAY,
                'times' => [
                    [
                        'from' => '09:00',
                        'to' => '11:00',
                    ],
                    [
                        'from' => '16:00',
                        'to' => '18:00',
                    ],
                ],
            ],
            [
                'day' => OpeningDayDto::DAY_WEDNESDAY,
                'times' => [
                    [
                        'from' => '09:00',
                        'to' => '11:00',
                    ],
                    [
                        'from' => '16:00',
                        'to' => '18:00',
                    ],
                ],
            ],
            [
                'day' => OpeningDayDto::DAY_THURSDAY,
                'times' => [
                    [
                        'from' => '09:00',
                        'to' => '11:00',
                    ],
                    [
                        'from' => '16:00',
                        'to' => '18:00',
                    ],
                ],
            ],
            [
                'day' => OpeningDayDto::DAY_FRIDAY,
                'times' => [
                    [
                        'from' => '10:00',
                        'to' => '13:00',
                    ],
                    [
                        'from' => '15:00',
                        'to' => '18:00',
                    ],
                ],
            ],
            [
                'day' => OpeningDayDto::DAY_SATURDAY,
                'times' => [
                    [
                        'from' => '07:00',
                        'to' => '12:00',
                    ],
                ],
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            OpeningDayFixtures::class,
            OpeningTimeFixtures::class,
            CityLocationFixtures::class,
        ];
    }
}
