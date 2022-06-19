<?php

declare(strict_types=1);

namespace App\Service;

use App\AppConstants;
use App\AppContext;
use App\Entity\OpeningTime;
use App\Exception\OpeningTimeNotFoundException;
use App\Repository\OpeningTimeRepository;
use Doctrine\ORM\EntityManagerInterface;

class OpeningTimeService implements OpeningTimeServiceInterface
{
    private OpeningTimeRepository $openingTimeRepository;
    
    private EntityManagerInterface $entityManager;

    private AppContext $appContext;

    public function __construct(
        OpeningTimeRepository $openingTimeRepository,
        EntityManagerInterface $entityManager,
        AppContext $appContext
    ) {
        $this->openingTimeRepository = $openingTimeRepository;
        $this->entityManager = $entityManager;
        $this->appContext = $appContext;
    }

    public function getOpeningTimeByUuid(string $uuid): OpeningTime
    {
        /** @var OpeningTime $openingTime */
        $openingTime = $this->openingTimeRepository->getItemByUuid($uuid);

        if (!$openingTime instanceof OpeningTime) {
            throw new OpeningTimeNotFoundException($uuid);
        }

        return $openingTime;
    }

    public function getOpeningTimeByTime(\DateTimeImmutable $time): OpeningTime
    {
        /** @var OpeningTime $openingTime */
        $openingTime = $this->openingTimeRepository->getOpeningTimeByTime($time);

        if (!$openingTime instanceof OpeningTime) {
            throw new OpeningTimeNotFoundException($time->format(AppConstants::FORMAT_TIME));
        }

        return $openingTime;
    }

    public function getOpeningTimesBetweenFromAndTo(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->openingTimeRepository->getOpeningTimesBetweenFromAndTo($from, $to);
    }

    public function getOpeningTimesForDay(string $day, array $openingDays): array
    {
        $openingTimesForDay = $this->getFilteredOpeningTimesForDay($day, $openingDays);
        $openingTimesBetweenFromAndTo = [];

        foreach ($openingTimesForDay as $openingTime) {
            try {
                $openingTimesBetweenFromAndTo[] = $this->getOpeningTimesBetweenFromAndTo(
                    $this->createDateTimeFromTime($openingTime['from']),
                    $this->createDateTimeFromTime($openingTime['to'])
                );
            } catch (\Throwable $e) {
                continue;
            }
        }

        return array_merge_recursive(...$openingTimesBetweenFromAndTo);
    }

    public function getOpeningTimes(): array
    {
        return $this->openingTimeRepository->findAll();
    }

    public function createDateTimeFromTime(string $time): \DateTimeImmutable
    {
        $formatPrefixTime = sprintf(
            AppConstants::FORMAT_PREFIX_TIME,
            $this->appContext->getContextYear()
        );

        return new \DateTimeImmutable(sprintf('%s %s', $formatPrefixTime, $time));
    }

    public function createOpeningTime(\DateTimeImmutable $time): OpeningTime
    {
        $openingTime = new OpeningTime();
        $openingTime->setTime($time);
        $openingTime->setCreatedAt(new \DateTimeImmutable());
        $openingTime->setUpdatedAt(null);

        $this->entityManager->persist($openingTime);
        $this->entityManager->flush();

        return $openingTime;
    }

    public function deleteAllOpeningTimes(): void
    {
        $this->entityManager->getConnection()->executeQuery('TRUNCATE `opening_time`');
    }

    private function getFilteredOpeningTimesForDay(string $day, array $openingDays): array
    {
        foreach ($openingDays as $openingDay) {
            if ($openingDay['day'] === $day) {
                return $openingDay['times'] ?? [];
            }
        }

        return [];
    }
}
