<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObject\CityDto;
use App\Entity\City;
use App\Entity\User;
use App\Service\CityServiceInterface;
use App\Service\Hydrator\CityHydratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractApiController
{
    private CityServiceInterface $cityService;

    private CityHydratorInterface $cityHydrator;

    public function __construct(
        CityServiceInterface $cityService,
        CityHydratorInterface $cityHydrator
    ) {
        $this->cityService = $cityService;
        $this->cityHydrator = $cityHydrator;
    }

    #[Route('/api/city/seoSlug/{seoSlug}', name: 'api.city.get.seoSlug', methods: ['GET'])]
    public function getCityBySeoSlug(string $seoSlug): Response
    {
        $city = $this->cityService->getCityBySeoSlug($seoSlug);

        return $this->buildJsonSuccessResponse($city);
    }

    #[Route('/api/city/id/{id}', name: 'api.city.get.id', methods: ['GET'])]
    public function getCityById(string $id): Response
    {
        $city = $this->cityService->getCityByUuid($id);

        return $this->buildJsonSuccessResponse($city);
    }

    #[Route('/api/city/list/paginated/{page}/{perPage}', name: 'api.city.get.all.paginated', methods: ['GET'])]
    public function getAllCitiesWithPagination(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_LIST_LIMIT): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->cityService->getRecentCitiesWithPagination($page, $perPage);

        return $this->buildJsonPaginationResponse($result, [City::GROUP_READ]);
    }

    #[Route('/api/city/create', name: 'api.city.create', methods: ['POST'])]
    public function createCity(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $cityDto = $this->createCityDto($request);
        $createdCity = $this->cityService->createCity($cityDto);

        return $this->buildJsonSuccessResponse($createdCity);
    }

    #[Route('/api/city/update', name: 'api.city.update', methods: ['POST'])]
    public function updateCity(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $cityDto = $this->createCityDto($request);
        $updatedCity = $this->cityService->updateCity($cityDto);

        return $this->buildJsonSuccessResponse($updatedCity);
    }

    private function createCityDto(Request $request): CityDto
    {
        $payload = $this->getPayloadFromRequest($request);

        return $this->cityHydrator->hydrateFromArray($payload);
    }
}
