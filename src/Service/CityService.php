<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\CityDto;
use App\Entity\City;
use App\Exception\CityNotFoundException;
use App\Repository\CityRepository;
use App\Repository\Result\PaginatedItemsResult;
use App\Service\Util\SanitizerInterface;
use App\Service\Validator\CityValidatorInterface;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;

class CityService implements CityServiceInterface
{
    private EntityManagerInterface $entityManager;

    private CityValidatorInterface $cityValidator;

    private SanitizerInterface $htmlSanitizer;

    private SlugifyInterface $slugify;

    public function __construct(
        EntityManagerInterface $entityManager,
        CityValidatorInterface $cityValidator,
        SanitizerInterface $htmlSanitizer,
        SlugifyInterface $slugify
    ) {
        $this->entityManager = $entityManager;
        $this->cityValidator = $cityValidator;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->slugify = $slugify;
    }

    public function getCityBySeoSlug(string $seoSlug): City
    {
        /** @var City $city */
        $city = $this->getCityRepository()->getItemBySeoSlug($seoSlug);

        if (null === $city) {
            throw new CityNotFoundException($seoSlug);
        }

        return $city;
    }

    public function getCityByUuid(string $uuid): City
    {
        /** @var City $city */
        $city = $this->getCityRepository()->getItemByUuid($uuid);

        if (null === $city) {
            throw new CityNotFoundException($uuid);
        }

        return $city;
    }

    public function getRecentCitiesWithPagination(int $page, int $citiesPerPage): PaginatedItemsResult
    {
        $query = $this->getCityRepository()->getRecentItemsQuery();

        return $this->getCityRepository()->getPaginatedItemsForQuery($query, $page, $citiesPerPage);
    }

    public function getTotalCitiesCount(bool $onlyFromToday = false): int
    {
        return $this->getCityRepository()->getTotalItemsCount($onlyFromToday);
    }

    public function createCity(CityDto $cityDto): City
    {
        $this->cityValidator->validateCity($cityDto);

        $cityName = $this->htmlSanitizer->sanitize($cityDto->getName());

        $city = new City();
        $city->setName($cityName);
        $city->setSeoSlug($this->slugify->slugify($cityName));
        $city->setCreatedAt(new \DateTimeImmutable());
        $city->setUpdatedAt(null);

        $this->entityManager->persist($city);
        $this->entityManager->flush();

        return $city;
    }

    public function updateCity(CityDto $cityDto): City
    {
        $this->cityValidator->validateCity($cityDto);

        $cityName = $this->htmlSanitizer->sanitize($cityDto->getName());

        $city = $this->getCityByUuid($cityDto->getId());
        $city->setName($cityName);
        $city->setSeoSlug($this->slugify->slugify($cityName));
        $city->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $city;
    }

    private function getCityRepository(): CityRepository
    {
        return $this->entityManager->getRepository(City::class);
    }
}
