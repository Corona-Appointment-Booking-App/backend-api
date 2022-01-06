<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\CityDto;
use App\Entity\City;
use App\Repository\Result\PaginatedItemsResult;

interface CityServiceInterface
{
    public function getCityBySeoSlug(string $seoSlug): City;

    public function getCityByUuid(string $uuid): City;

    public function getRecentCitiesWithPagination(int $page, int $citiesPerPage): PaginatedItemsResult;

    public function getTotalCitiesCount(bool $onlyFromToday = false): int;

    public function createCity(CityDto $cityDto): City;

    public function updateCity(CityDto $cityDto): City;
}
