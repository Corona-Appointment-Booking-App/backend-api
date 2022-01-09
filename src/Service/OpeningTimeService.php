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
    private EntityManagerInterface $entityManager;

    private AppContext $appContext;

    public function __construct(
        EntityManagerInterface $entityManager,
        AppContext $appContext
    ) {
        $this->entityManager = $entityManager;
        $this->appContext = $appContext;
    }

    public function getOpeningTimeByUuid(string $uuid): OpeningTime
    {
        /** @var OpeningTime $openingTime */
        $openingTime = $this->getOpeningTimeRepository()->getItemByUuid($uuid);

        if (null === $openingTime) {
            throw new OpeningTimeNotFoundException($uuid);
        }

        return $openingTime;
    }

    public function getOpeningTimeByTime(\DateTimeImmutable $time): OpeningTime
    {
        /** @var OpeningTime $openingTime */
        $openingTime = $this->getOpeningTimeRepository()->getOpeningTimeByTime($time);

        if (null === $openingTime) {
            throw new OpeningTimeNotFoundException($time->format(AppConstants::FORMAT_TIME));
        }

        return $openingTime;
    }

    public function getOpeningTimesBetweenFromAndTo(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->getOpeningTimeRepository()->getOpeningTimesBetweenFromAndTo($from, $to);
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
        return $this->getOpeningTimeRepository()->findAll();
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

    private function getOpeningTimeRepository(): OpeningTimeRepository
    {
        return $this->entityManager->getRepository(OpeningTime::class);
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
