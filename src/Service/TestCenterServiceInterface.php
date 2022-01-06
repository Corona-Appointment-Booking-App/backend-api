<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\TestCenterDto;
use App\Entity\TestCenter;
use App\Repository\Result\PaginatedItemsResult;

interface TestCenterServiceInterface
{
    public function getTestCenterBySeoSlug(string $seoSlug): TestCenter;

    public function getTestCenterByUuid(string $uuid): TestCenter;

    public function getRecentTestCentersWithPagination(int $page, int $testCentersPerPage): PaginatedItemsResult;

    public function getTotalTestCentersCount(bool $onlyFromToday = false): int;

    public function createTestCenter(TestCenterDto $testCenterDto): TestCenter;

    public function updateTestCenter(TestCenterDto $testCenterDto): TestCenter;
}
