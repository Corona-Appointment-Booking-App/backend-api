<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\TestCenterDto;
use App\Entity\TestCenter;
use App\Exception\TestCenterNotFoundException;
use App\Repository\Result\PaginatedItemsResult;
use App\Repository\TestCenterRepository;
use App\Service\Util\SanitizerInterface;
use App\Service\Validator\TestCenterValidatorInterface;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;

class TestCenterService implements TestCenterServiceInterface
{
    private TestCenterRepository $testCenterRepository;
    
    private EntityManagerInterface $entityManager;

    private TestCenterValidatorInterface $testCenterValidator;

    private CityLocationService $cityLocationService;

    private SanitizerInterface $htmlSanitizer;

    private SlugifyInterface $slugify;

    public function __construct(
        TestCenterRepository $testCenterRepository,
        EntityManagerInterface $entityManager,
        TestCenterValidatorInterface $testCenterValidator,
        CityLocationService $cityLocationService,
        SanitizerInterface $htmlSanitizer,
        SlugifyInterface $slugify
    ) {
        $this->testCenterRepository = $testCenterRepository;
        $this->entityManager = $entityManager;
        $this->testCenterValidator = $testCenterValidator;
        $this->cityLocationService = $cityLocationService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->slugify = $slugify;
    }

    public function getTestCenterBySeoSlug(string $seoSlug): TestCenter
    {
        /** @var TestCenter $testCenter */
        $testCenter = $this->testCenterRepository->getItemBySeoSlug($seoSlug);

        if (!$testCenter instanceof TestCenter) {
            throw new TestCenterNotFoundException($seoSlug);
        }

        return $testCenter;
    }

    public function getTestCenterByUuid(string $uuid): TestCenter
    {
        /** @var TestCenter $testCenter */
        $testCenter = $this->testCenterRepository->getItemByUuid($uuid);

        if (!$testCenter instanceof TestCenter) {
            throw new TestCenterNotFoundException($uuid);
        }

        return $testCenter;
    }

    public function getRecentTestCentersWithPagination(int $page, int $testCentersPerPage): PaginatedItemsResult
    {
        $query = $this->testCenterRepository->getRecentItemsQuery();

        return $this->testCenterRepository->getPaginatedItemsForQuery($query, $page, $testCentersPerPage);
    }

    public function getTotalTestCentersCount(bool $onlyFromToday = false): int
    {
        return $this->testCenterRepository->getTotalItemsCount($onlyFromToday);
    }

    public function createTestCenter(TestCenterDto $testCenterDto): TestCenter
    {
        $this->testCenterValidator->validateTestCenter($testCenterDto);

        $testCenterName = $this->htmlSanitizer->sanitize($testCenterDto->getName());

        $testCenter = new TestCenter();
        $testCenter->setCityLocation($this->cityLocationService->getCityLocationByUuid($testCenterDto->getCityLocationId()));
        $testCenter->setName($testCenterName);
        $testCenter->setAddress($this->htmlSanitizer->sanitize($testCenterDto->getAddress()));
        $testCenter->setSeoSlug($this->slugify->slugify($testCenterName));
        $testCenter->setOpeningDays($testCenterDto->getOpeningDays());
        $testCenter->setCreatedAt(new \DateTimeImmutable());
        $testCenter->setUpdatedAt(null);

        $this->entityManager->persist($testCenter);
        $this->entityManager->flush();

        return $testCenter;
    }

    public function updateTestCenter(TestCenterDto $testCenterDto): TestCenter
    {
        $this->testCenterValidator->validateTestCenter($testCenterDto);

        $testCenterName = $this->htmlSanitizer->sanitize($testCenterDto->getName());

        $testCenter = $this->getTestCenterByUuid($testCenterDto->getId());
        $testCenter->setCityLocation($this->cityLocationService->getCityLocationByUuid($testCenterDto->getCityLocationId()));
        $testCenter->setName($testCenterName);
        $testCenter->setAddress($this->htmlSanitizer->sanitize($testCenterDto->getAddress()));
        $testCenter->setSeoSlug($this->slugify->slugify($testCenterName));
        $testCenter->setOpeningDays($testCenterDto->getOpeningDays());
        $testCenter->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $testCenter;
    }
}
