<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\CityLocationDto;
use App\Entity\CityLocation;
use App\Repository\Result\PaginatedItemsResult;

interface CityLocationServiceInterface
{
    public function getCityLocationBySeoSlug(string $seoSlug): CityLocation;

    public function getCityLocationByUuid(string $uuid): CityLocation;

    public function getRecentCityLocationsWithPagination(int $page, int $cityLocationsPerPage): PaginatedItemsResult;

    public function getTotalCityLocationsCount(bool $onlyFromToday = false): int;

    public function createCityLocation(CityLocationDto $cityLocationDto): CityLocation;

    public function updateCityLocation(CityLocationDto $cityLocationDto): CityLocation;
}
