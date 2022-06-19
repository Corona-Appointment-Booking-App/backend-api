<?php

declare(strict_types=1);

namespace App\Service;

use App\DataTransferObject\CityLocationDto;
use App\Entity\CityLocation;
use App\Exception\CityLocationNotFoundException;
use App\Repository\CityLocationRepository;
use App\Repository\Result\PaginatedItemsResult;
use App\Service\Util\SanitizerInterface;
use App\Service\Validator\CityLocationValidatorInterface;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;

class CityLocationService implements CityLocationServiceInterface
{
    private CityLocationRepository $cityLocationRepository;
    
    private EntityManagerInterface $entityManager;

    private CityLocationValidatorInterface $cityLocationValidator;

    private CityServiceInterface $cityService;

    private SanitizerInterface $htmlSanitizer;

    private SlugifyInterface $slugify;

    public function __construct(
        CityLocationRepository $cityLocationRepository,
        EntityManagerInterface $entityManager,
        CityLocationValidatorInterface $cityLocationValidator,
        CityServiceInterface $cityService,
        SanitizerInterface $htmlSanitizer,
        SlugifyInterface $slugify
    ) {
        $this->cityLocationRepository = $cityLocationRepository;
        $this->entityManager = $entityManager;
        $this->cityLocationValidator = $cityLocationValidator;
        $this->cityService = $cityService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->slugify = $slugify;
    }

    public function getCityLocationBySeoSlug(string $seoSlug): CityLocation
    {
        /** @var CityLocation $cityLocation */
        $cityLocation = $this->cityLocationRepository->getItemBySeoSlug($seoSlug);

        if (!$cityLocation instanceof CityLocation) {
            throw new CityLocationNotFoundException($seoSlug);
        }

        return $cityLocation;
    }

    public function getCityLocationByUuid(string $uuid): CityLocation
    {
        /** @var CityLocation $cityLocation */
        $cityLocation = $this->cityLocationRepository->getItemByUuid($uuid);

        if (!$cityLocation instanceof CityLocation) {
            throw new CityLocationNotFoundException($uuid);
        }

        return $cityLocation;
    }

    public function getRecentCityLocationsWithPagination(int $page, int $cityLocationsPerPage): PaginatedItemsResult
    {
        $query = $this->cityLocationRepository->getRecentItemsQuery();

        return $this->cityLocationRepository->getPaginatedItemsForQuery($query, $page, $cityLocationsPerPage);
    }

    public function getTotalCityLocationsCount(bool $onlyFromToday = false): int
    {
        return $this->cityLocationRepository->getTotalItemsCount($onlyFromToday);
    }

    public function createCityLocation(CityLocationDto $cityLocationDto): CityLocation
    {
        $this->cityLocationValidator->validateCityLocation($cityLocationDto);

        $cityLocationName = $this->htmlSanitizer->sanitize($cityLocationDto->getName());

        $cityLocation = new CityLocation();
        $cityLocation->setCity($this->cityService->getCityByUuid($cityLocationDto->getCityId()));
        $cityLocation->setName($cityLocationName);
        $cityLocation->setSeoSlug($this->slugify->slugify($cityLocationName));
        $cityLocation->setCreatedAt(new \DateTimeImmutable());
        $cityLocation->setUpdatedAt(null);

        $this->entityManager->persist($cityLocation);
        $this->entityManager->flush();

        return $cityLocation;
    }

    public function updateCityLocation(CityLocationDto $cityLocationDto): CityLocation
    {
        $this->cityLocationValidator->validateCityLocation($cityLocationDto);

        $cityLocationName = $this->htmlSanitizer->sanitize($cityLocationDto->getName());

        $cityLocation = $this->getCityLocationByUuid($cityLocationDto->getId());
        $cityLocation->setCity($this->cityService->getCityByUuid($cityLocationDto->getCityId()));
        $cityLocation->setName($cityLocationName);
        $cityLocation->setSeoSlug($this->slugify->slugify($cityLocationName));
        $cityLocation->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $cityLocation;
    }
}
