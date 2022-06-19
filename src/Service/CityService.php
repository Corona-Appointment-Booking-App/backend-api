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
    private CityRepository $cityRepository;

    private EntityManagerInterface $entityManager;

    private CityValidatorInterface $cityValidator;

    private SanitizerInterface $htmlSanitizer;

    private SlugifyInterface $slugify;

    public function __construct(
        CityRepository $cityRepository,
        EntityManagerInterface $entityManager,
        CityValidatorInterface $cityValidator,
        SanitizerInterface $htmlSanitizer,
        SlugifyInterface $slugify
    ) {
        $this->cityRepository = $cityRepository;
        $this->entityManager = $entityManager;
        $this->cityValidator = $cityValidator;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->slugify = $slugify;
    }

    public function getCityBySeoSlug(string $seoSlug): City
    {
        /** @var City $city */
        $city = $this->cityRepository->getItemBySeoSlug($seoSlug);

        if (!$city instanceof City) {
            throw new CityNotFoundException($seoSlug);
        }

        return $city;
    }

    public function getCityByUuid(string $uuid): City
    {
        /** @var City $city */
        $city = $this->cityRepository->getItemByUuid($uuid);

        if (!$city instanceof City) {
            throw new CityNotFoundException($uuid);
        }

        return $city;
    }

    public function getRecentCitiesWithPagination(int $page, int $citiesPerPage): PaginatedItemsResult
    {
        $query = $this->cityRepository->getRecentItemsQuery();

        return $this->cityRepository->getPaginatedItemsForQuery($query, $page, $citiesPerPage);
    }

    public function getTotalCitiesCount(bool $onlyFromToday = false): int
    {
        return $this->cityRepository->getTotalItemsCount($onlyFromToday);
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
}
