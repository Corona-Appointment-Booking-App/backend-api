<?php

declare(strict_types=1);

namespace App\Controller;

use App\AppConstants;
use App\Entity\OpeningDay;
use App\Entity\OpeningTime;
use App\Entity\User;
use App\Service\OpeningDayServiceInterface;
use App\Service\OpeningTimeServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpeningDayController extends AbstractApiController
{
    private OpeningDayServiceInterface $openingDayService;

    private OpeningTimeServiceInterface $openingTimeService;

    public function __construct(
        OpeningDayServiceInterface $openingDayService,
        OpeningTimeServiceInterface $openingTimeService
    ) {
        $this->openingDayService = $openingDayService;
        $this->openingTimeService = $openingTimeService;
    }

    #[Route('/api/opening-day/generate', name: 'api.opening-day.generate')]
    public function generateOpeningDays(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $data = [
            'openingDays' => [],
            'openingTimes' => [],
        ];

        foreach ($this->openingDayService->getOpeningDays() as $openingDay) {
            /* @var OpeningDay $openingDay */
            $data['openingDays'][] = $openingDay->getDay();
        }

        foreach ($this->openingTimeService->getOpeningTimes() as $openingTime) {
            /* @var OpeningTime $openingTime */
            $data['openingTimes'][] = $openingTime->getTime()->format(AppConstants::FORMAT_TIME);
        }

        return $this->json(['success' => true, 'data' => $data]);
    }
}
