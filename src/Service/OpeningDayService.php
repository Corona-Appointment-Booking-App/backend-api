<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\OpeningDay;
use App\Exception\OpeningDayNotFoundException;
use App\Repository\OpeningDayRepository;
use App\Service\Util\SanitizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class OpeningDayService implements OpeningDayServiceInterface
{
    private OpeningDayRepository $openingDayRepository;

    private EntityManagerInterface $entityManager;

    private SanitizerInterface $htmlSanitizer;

    public function __construct(
        OpeningDayRepository $openingDayRepository,
        EntityManagerInterface $entityManager,
        SanitizerInterface $htmlSanitizer
    ) {
        $this->openingDayRepository = $openingDayRepository;
        $this->entityManager = $entityManager;
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function getOpeningDayByUuid(string $uuid): OpeningDay
    {
        /** @var OpeningDay $openingDay */
        $openingDay = $this->openingDayRepository->getItemByUuid($uuid);

        if (!$openingDay instanceof OpeningDay) {
            throw new OpeningDayNotFoundException($uuid);
        }

        return $openingDay;
    }

    public function getOpeningDayByDay(string $day): OpeningDay
    {
        /** @var OpeningDay $openingDay */
        $openingDay = $this->openingDayRepository->getOpeningDayByDay($day);

        if (!$openingDay instanceof OpeningDay) {
            throw new OpeningDayNotFoundException($day);
        }

        return $openingDay;
    }

    public function getOpeningDays(): array
    {
        return $this->openingDayRepository->findAll();
    }

    public function createOpeningDay(string $day): OpeningDay
    {
        $openingDay = new OpeningDay();
        $openingDay->setDay($this->htmlSanitizer->sanitize($day));
        $openingDay->setCreatedAt(new \DateTimeImmutable());
        $openingDay->setUpdatedAt(null);

        $this->entityManager->persist($openingDay);
        $this->entityManager->flush();

        return $openingDay;
    }

    public function deleteAllOpeningDays(): void
    {
        $this->entityManager->getConnection()->executeQuery('TRUNCATE `opening_day`');
    }
}
