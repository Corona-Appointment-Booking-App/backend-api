<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\BookingServiceInterface;
use App\Service\CityLocationServiceInterface;
use App\Service\CityServiceInterface;
use App\Service\TestCenterServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractApiController
{
    private BookingServiceInterface $bookingService;

    private CityServiceInterface $cityService;

    private CityLocationServiceInterface $cityLocationService;

    private TestCenterServiceInterface $testCenterService;

    public function __construct(
        BookingServiceInterface $bookingService,
        CityServiceInterface $cityService,
        CityLocationServiceInterface $cityLocationService,
        TestCenterServiceInterface $testCenterService
    ) {
        $this->bookingService = $bookingService;
        $this->cityService = $cityService;
        $this->cityLocationService = $cityLocationService;
        $this->testCenterService = $testCenterService;
    }

    #[Route('/api/dashboard', name: 'api.dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = [
            'bookingsCount' => $this->bookingService->getTotalBookingsCount(),
            'bookingsCountToday' => $this->bookingService->getTotalBookingsCount(true),
            'citiesCount' => $this->cityService->getTotalCitiesCount(),
            'cityLocationsCount' => $this->cityLocationService->getTotalCityLocationsCount(),
            'testCentersCount' => $this->testCenterService->getTotalTestCentersCount(),
        ];

        return $this->json(['success' => true, 'data' => $data]);
    }
}
