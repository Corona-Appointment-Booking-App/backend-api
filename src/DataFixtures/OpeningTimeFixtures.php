<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Service\Generator\OpeningTimeGeneratorInterface;
use App\Service\OpeningTimeServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OpeningTimeFixtures extends Fixture
{
    private OpeningTimeGeneratorInterface $openingTimeGenerator;

    private OpeningTimeServiceInterface $openingTimeService;

    public function __construct(
        OpeningTimeGeneratorInterface $openingTimeGenerator,
        OpeningTimeServiceInterface $openingTimeService
    ) {
        $this->openingTimeGenerator = $openingTimeGenerator;
        $this->openingTimeService = $openingTimeService;
    }

    public function load(ObjectManager $manager): void
    {
        $generatedOpeningTimes = $this->openingTimeGenerator->generateOpeningTimes();
        foreach ($generatedOpeningTimes as $openingTime) {
            $time = $this->openingTimeService->createDateTimeFromTime($openingTime);
            $this->openingTimeService->createOpeningTime($time);
        }
    }
}
