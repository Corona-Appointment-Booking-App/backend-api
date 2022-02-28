<?php

declare(strict_types=1);

namespace App\Controller;

use App\AppConstants;
use App\DataTransferObject\OpeningDayCollection;
use App\Entity\Booking;
use App\Entity\TestCenter;
use App\Entity\User;
use App\Exception\BookingNotFoundException;
use App\Service\BookingServiceInterface;
use App\Service\Generator\BookingFinalizeTokenGeneratorInterface;
use App\Service\Generator\OpeningDayGeneratorListInterface;
use App\Service\Hydrator\BookingHydratorInterface;
use App\Service\OpeningTimeServiceInterface;
use App\Service\TestCenterServiceInterface;
use App\Service\Validator\BookingFinalizeTokenValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractApiController
{
    private BookingServiceInterface $bookingService;

    private BookingHydratorInterface $bookingHydrator;

    private BookingFinalizeTokenGeneratorInterface $bookingFinalizeTokenGenerator;

    private BookingFinalizeTokenValidatorInterface $bookingFinalizeTokenValidator;

    private OpeningTimeServiceInterface $openingTimeService;

    private OpeningDayGeneratorListInterface $openingDayGeneratorList;

    private TestCenterServiceInterface $testCenterService;

    public function __construct(
        BookingServiceInterface $bookingService,
        BookingHydratorInterface $bookingHydrator,
        BookingFinalizeTokenGeneratorInterface $bookingFinalizeTokenGenerator,
        BookingFinalizeTokenValidatorInterface $bookingFinalizeTokenValidator,
        OpeningTimeServiceInterface $openingTimeService,
        OpeningDayGeneratorListInterface $openingDayGeneratorList,
        TestCenterServiceInterface $testCenterService
    ) {
        $this->bookingService = $bookingService;
        $this->bookingHydrator = $bookingHydrator;
        $this->bookingFinalizeTokenGenerator = $bookingFinalizeTokenGenerator;
        $this->bookingFinalizeTokenValidator = $bookingFinalizeTokenValidator;
        $this->openingTimeService = $openingTimeService;
        $this->openingDayGeneratorList = $openingDayGeneratorList;
        $this->testCenterService = $testCenterService;
    }

    #[Route('/api/booking/id/{id}', name: 'api.booking.get.id', methods: ['GET'])]
    public function getBookingById(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $booking = $this->bookingService->getBookingByUuid($id);

        return $this->buildJsonSuccessResponse($booking);
    }

    #[Route('/api/booking/list/paginated/{page}/{perPage}', name: 'api.booking.get.all.paginated', methods: ['GET'])]
    public function getAllBookingsWithPagination(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_LIST_LIMIT): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $result = $this->bookingService->getRecentBookingsWithPagination($page, $perPage);

        return $this->buildJsonPaginationResponse($result, [Booking::GROUP_READ]);
    }

    #[Route('/api/booking/cancel/{id}', name: 'api.booking.cancel.id', methods: ['GET'])]
    public function cancelBookingById(string $id): Response
    {
        $this->bookingService->cancelBookingByUuid($id);

        return $this->json(['success' => true]);
    }

    #[Route('/api/booking/checkout', name: 'api.booking.checkout', methods: ['POST'])]
    public function bookingCheckout(Request $request): Response
    {
        $payload = $this->getPayloadFromRequest($request);
        $testCenterId = $payload['testCenterId'] ??= '';
        $selectedOpeningDayDate = (string) $payload['selectedOpeningDayDate'] ??= '';

        $testCenter = $this->testCenterService->getTestCenterByUuid($testCenterId);
        $cityLocation = $testCenter->getCityLocation();
        $availableOpeningTimes = $this->getAvailableOpeningTimes($testCenter, $selectedOpeningDayDate);

        return $this->json([
            'success' => true,
            'data' => [
                'testCenter' => [
                    'id' => $testCenter->getUuid()->toRfc4122(),
                    'name' => $testCenter->getName(),
                    'address' => $testCenter->getAddress(),
                    'seoSlug' => $testCenter->getSeoSlug(),
                    'cityLocation' => [
                        'id' => $cityLocation->getUuid()->toRfc4122(),
                        'name' => $cityLocation->getName(),
                        'seoSlug' => $cityLocation->getSeoSlug(),
                    ],
                ],
                'availableOpeningTimes' => $availableOpeningTimes,
            ],
        ]);
    }

    #[Route('/api/booking/checkout/fetch-token', name: 'api.booking.checkout.fetch-token', methods: ['POST'])]
    public function bookingFetchToken(Request $request): Response
    {
        $payload = $this->getPayloadFromRequest($request);
        $generatedToken = $this->generateTokenFromPayload($payload);

        return $this->json(['success' => true, 'token' => $generatedToken]);
    }

    #[Route('/api/booking/checkout/finalize', name: 'api.booking.checkout.finalize', methods: ['POST'])]
    public function bookingFinalize(Request $request): Response
    {
        $payload = $this->getPayloadFromRequest($request);
        $token = (string) $payload['token'] ??= '';

        $generatedToken = $this->generateTokenFromPayload($payload);
        $this->bookingFinalizeTokenValidator->validateGeneratedToken($generatedToken, $token);

        $bookingDto = $this->bookingHydrator->hydrateFromArray($payload);
        $this->bookingService->createBooking($bookingDto);

        return $this->json(['success' => true]);
    }

    #[Route('/api/booking/create', name: 'api.booking.create', methods: ['POST'])]
    public function createBooking(Request $request): Response
    {
        $payload = $this->getPayloadFromRequest($request);
        $bookingDto = $this->bookingHydrator->hydrateFromArray($payload);

        $createdBooking = $this->bookingService->createBooking($bookingDto);

        return $this->buildJsonSuccessResponse($createdBooking);
    }

    private function isTimeForTestCenterBookedOut(TestCenter $testCenter, \DateTimeImmutable $time): bool
    {
        try {
            $this->bookingService->getBookingByTestCenterAndTime($testCenter, $time);

            return true;
        } catch (BookingNotFoundException $e) {
            return false;
        }
    }

    private function getOpeningTimeForSelectedOpeningDay(string $selectedOpeningDay, \DateTimeImmutable $openingTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable(sprintf('%s %s', $selectedOpeningDay, $openingTime->format('H:i:s')));
    }

    private function getAvailableOpeningTimes(TestCenter $testCenter, string $selectedOpeningDayDate): array
    {
        $openingDaysCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($testCenter->getOpeningDays());
        $generatedOpeningDaysForDate = $this->openingDayGeneratorList->generateOpeningDaysForDate($openingDaysCollection, $selectedOpeningDayDate);
        if (null === $generatedOpeningDaysForDate) {
            return [];
        }

        $openingTimesForDay = $this->openingTimeService->getOpeningTimesForDay($generatedOpeningDaysForDate->getDay(), $testCenter->getOpeningDays());

        $availableOpeningTimes = [];
        foreach ($openingTimesForDay as $openingTimeForDay) {
            try {
                $openingTime = $this->openingTimeService->getOpeningTimeByTime($openingTimeForDay->getTime());
                $openingTimeForSelectedOpeningDayDate = $this->getOpeningTimeForSelectedOpeningDay($selectedOpeningDayDate, $openingTime->getTime());
                $availableOpeningTimes[] = [
                    'id' => $openingTime->getUuid(),
                    'date' => $generatedOpeningDaysForDate->getDate(),
                    'time' => $openingTime->getTime()->format(AppConstants::FORMAT_TIME),
                    'isBookedOut' => $this->isTimeForTestCenterBookedOut($testCenter, $openingTimeForSelectedOpeningDayDate),
                ];
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $availableOpeningTimes;
    }

    private function generateTokenFromPayload(array $payload): string
    {
        $participants = (array) $payload['participants'] ??= [];
        $selectedOpeningDayDate = (string) $payload['selectedOpeningDayDate'] ??= '';
        $selectedOpeningTime = (string) $payload['selectedOpeningTime'] ??= '';
        $selectedTestCenterId = (string) $payload['selectedTestCenterId'] ??= '';

        return $this->bookingFinalizeTokenGenerator->generateToken(
            $participants,
            $selectedOpeningDayDate,
            $selectedOpeningTime,
            $selectedTestCenterId
        );
    }
}
