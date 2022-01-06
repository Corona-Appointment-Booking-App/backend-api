<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\CityLocationDto;
use App\Entity\CityLocation;
use App\Entity\User;
use App\Service\CityLocationServiceInterface;
use App\Service\Hydrator\CityLocationHydratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityLocationController extends AbstractApiController
{
    private CityLocationServiceInterface $cityLocationService;

    private CityLocationHydratorInterface $cityLocationHydrator;

    public function __construct(
        CityLocationServiceInterface $cityLocationService,
        CityLocationHydratorInterface $cityLocationHydrator
    ) {
        $this->cityLocationService = $cityLocationService;
        $this->cityLocationHydrator = $cityLocationHydrator;
    }

    #[Route('/api/city/location/seoSlug/{seoSlug}', name: 'api.city.location.get.seoSlug', methods: ['GET'])]
    public function getCityLocationBySeoSlug(string $seoSlug): Response
    {
        $cityLocation = $this->cityLocationService->getCityLocationBySeoSlug($seoSlug);

        return $this->buildJsonSuccessResponse($cityLocation);
    }

    #[Route('/api/city/location/id/{id}', name: 'api.city.location.get.id', methods: ['GET'])]
    public function getCityLocationById(string $id): Response
    {
        $cityLocation = $this->cityLocationService->getCityLocationByUuid($id);

        return $this->buildJsonSuccessResponse($cityLocation);
    }

    #[Route('/api/city/location/list/paginated/{page}/{perPage}', name: 'api.city.location.get.all.paginated', methods: ['GET'])]
    public function getAllCityLocationsWithPagination(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_LIST_LIMIT): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->cityLocationService->getRecentCityLocationsWithPagination($page, $perPage);

        return $this->buildJsonPaginationResponse($result, [CityLocation::GROUP_READ]);
    }

    #[Route('/api/city/location/create', name: 'api.city.location.create', methods: ['POST'])]
    public function createCityLocation(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $cityLocationDto = $this->createCityLocationDto($request);
        $createdCityLocation = $this->cityLocationService->createCityLocation($cityLocationDto);

        return $this->buildJsonSuccessResponse($createdCityLocation);
    }

    #[Route('/api/city/location/update', name: 'api.city.location.update', methods: ['POST'])]
    public function updateCityLocation(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $cityLocationDto = $this->createCityLocationDto($request);
        $updatedCityLocation = $this->cityLocationService->updateCityLocation($cityLocationDto);

        return $this->buildJsonSuccessResponse($updatedCityLocation);
    }

    private function createCityLocationDto(Request $request): CityLocationDto
    {
        $payload = $this->getPayloadFromRequest($request);

        return $this->cityLocationHydrator->hydrateFromArray($payload);
    }
}
