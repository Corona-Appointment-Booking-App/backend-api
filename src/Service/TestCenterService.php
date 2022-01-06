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
    private EntityManagerInterface $entityManager;

    private TestCenterValidatorInterface $testCenterValidator;

    private CityLocationService $cityLocationService;

    private SanitizerInterface $htmlSanitizer;

    private SlugifyInterface $slugify;

    public function __construct(
        EntityManagerInterface $entityManager,
        TestCenterValidatorInterface $testCenterValidator,
        CityLocationService $cityLocationService,
        SanitizerInterface $htmlSanitizer,
        SlugifyInterface $slugify
    ) {
        $this->entityManager = $entityManager;
        $this->testCenterValidator = $testCenterValidator;
        $this->cityLocationService = $cityLocationService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->slugify = $slugify;
    }

    public function getTestCenterBySeoSlug(string $seoSlug): TestCenter
    {
        /** @var TestCenter $testCenter */
        $testCenter = $this->getTestCenterRepository()->getItemBySeoSlug($seoSlug);

        if (null === $testCenter) {
            throw new TestCenterNotFoundException($seoSlug);
        }

        return $testCenter;
    }

    public function getTestCenterByUuid(string $uuid): TestCenter
    {
        /** @var TestCenter $testCenter */
        $testCenter = $this->getTestCenterRepository()->getItemByUuid($uuid);

        if (null === $testCenter) {
            throw new TestCenterNotFoundException($uuid);
        }

        return $testCenter;
    }

    public function getRecentTestCentersWithPagination(int $page, int $testCentersPerPage): PaginatedItemsResult
    {
        $query = $this->getTestCenterRepository()->getRecentItemsQuery();

        return $this->getTestCenterRepository()->getPaginatedItemsForQuery($query, $page, $testCentersPerPage);
    }

    public function getTotalTestCentersCount(bool $onlyFromToday = false): int
    {
        return $this->getTestCenterRepository()->getTotalItemsCount($onlyFromToday);
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

    private function getTestCenterRepository(): TestCenterRepository
    {
        return $this->entityManager->getRepository(TestCenter::class);
    }
}
