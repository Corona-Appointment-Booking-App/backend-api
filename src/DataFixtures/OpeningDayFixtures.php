<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataTransferObject\OpeningDayDto;
use App\Service\OpeningDayServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OpeningDayFixtures extends Fixture
{
    private OpeningDayServiceInterface $openingDayService;

    public function __construct(OpeningDayServiceInterface $openingDayService)
    {
        $this->openingDayService = $openingDayService;
    }

    public function load(ObjectManager $manager): void
    {
        $openingDays = $this->getOpeningDays();

        foreach ($openingDays as $openingDay) {
            $this->openingDayService->createOpeningDay($openingDay);
        }
    }

    private function getOpeningDays(): array
    {
        return [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
            OpeningDayDto::DAY_SUNDAY,
        ];
    }
}
