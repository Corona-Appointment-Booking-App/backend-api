<?php

declare(strict_types=1);

namespace App\Service;

use App\AppConstants;
use App\DataTransferObject\BookingDto;
use App\Entity\Booking;
use App\Entity\BookingParticipant;
use App\Entity\TestCenter;
use App\Exception\BookingAlreadyExistsException;
use App\Exception\BookingNotAllowedException;
use App\Exception\BookingNotFoundException;
use App\Repository\BookingRepository;
use App\Repository\Result\PaginatedItemsResult;
use App\Service\Util\SanitizerInterface;
use App\Service\Validator\BookingValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class BookingService implements BookingServiceInterface
{
    private EntityManagerInterface $entityManager;

    private TestCenterServiceInterface $testCenterService;

    private OpeningTimeServiceInterface $openingTimeService;

    private BookingValidatorInterface $bookingValidator;

    private SanitizerInterface $htmlSanitizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        TestCenterServiceInterface $testCenterService,
        OpeningTimeServiceInterface $openingTimeService,
        BookingValidatorInterface $bookingValidator,
        SanitizerInterface $htmlSanitizer
    ) {
        $this->entityManager = $entityManager;
        $this->testCenterService = $testCenterService;
        $this->openingTimeService = $openingTimeService;
        $this->bookingValidator = $bookingValidator;
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function getBookingByUuid(string $uuid): Booking
    {
        /** @var Booking $booking */
        $booking = $this->getBookingRepository()->getItemByUuid($uuid);

        if (null === $booking) {
            throw new BookingNotFoundException($uuid);
        }

        return $booking;
    }

    public function getBookingByTestCenterAndTime(TestCenter $testCenter, \DateTimeImmutable $time): Booking
    {
        /** @var Booking $booking */
        $booking = $this->getBookingRepository()->getBookingByTestCenterAndTime($testCenter, $time);

        if (null === $booking) {
            throw new BookingNotFoundException($testCenter->getUuid()->toRfc4122());
        }

        return $booking;
    }

    public function getRecentBookingsWithPagination(int $page, int $bookingsPerPage): PaginatedItemsResult
    {
        $query = $this->getBookingRepository()->getRecentItemsQuery();

        return $this->getBookingRepository()->getPaginatedItemsForQuery($query, $page, $bookingsPerPage);
    }

    public function getTotalBookingsCount(bool $onlyFromToday = false): int
    {
        return $this->getBookingRepository()->getTotalItemsCount($onlyFromToday);
    }

    public function createBooking(BookingDto $bookingDto): Booking
    {
        $this->bookingValidator->validateBooking($bookingDto);

        $testCenter = $this->testCenterService->getTestCenterByUuid($bookingDto->getTestCenterId());

        $booking = new Booking();
        $booking->setTestCenter($testCenter);
        $booking->setTime($bookingDto->getBookingTime());

        foreach ($bookingDto->getParticipants() as $participantDto) {
            $participant = new BookingParticipant();
            $participant->setBooking($booking);
            $participant->setFirstName($this->htmlSanitizer->sanitize($participantDto->getFirstName()));
            $participant->setLastName($this->htmlSanitizer->sanitize($participantDto->getLastName()));
            $participant->setBirthDate($participantDto->getBirthDate());
            $participant->setStreet($this->htmlSanitizer->sanitize($participantDto->getStreet()));
            $participant->setHouseNumber($this->htmlSanitizer->sanitize($participantDto->getHouseNumber()));
            $participant->setZipCode($this->htmlSanitizer->sanitize($participantDto->getZipCode()));
            $participant->setCity($this->htmlSanitizer->sanitize($participantDto->getCity()));
            $participant->setPhoneNumber($this->htmlSanitizer->sanitize($participantDto->getPhoneNumber()));
            $participant->setEmail($this->htmlSanitizer->sanitize($participantDto->getEmail()));
            $participant->setCreatedAt(new \DateTimeImmutable());
            $participant->setUpdatedAt(null);

            $booking->addParticipant($participant);
        }

        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setUpdatedAt(null);

        $this->validateBookingTimeIsValid($booking);
        $this->validateIsBooked($booking);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }

    private function validateBookingTimeIsValid(Booking $booking): void
    {
        $bookingDay = mb_strtolower($booking->getTime()->format(AppConstants::FORMAT_DAY));
        $bookingTime = $booking->getTime()->format(AppConstants::FORMAT_TIME);
        $openingTimesForDay = $this->openingTimeService->getOpeningTimesForDay($bookingDay, $booking->getTestCenter()->getOpeningDays());

        if (empty($openingTimesForDay)) {
            throw new BookingNotAllowedException($booking->getTestCenter()->getUuid()->toRfc4122(), $booking->getTime());
        }

        $allowedOpeningTimes = [];

        foreach ($openingTimesForDay as $openingTimeForDay) {
            try {
                $openingTime = $this->openingTimeService->getOpeningTimeByTime($openingTimeForDay->getTime());
                $allowedOpeningTimes[] = $openingTime->getTime()->format(AppConstants::FORMAT_TIME);
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (!\in_array($bookingTime, $allowedOpeningTimes, true)) {
            throw new BookingNotAllowedException($booking->getTestCenter()->getUuid()->toRfc4122(), $booking->getTime());
        }
    }

    private function validateIsBooked(Booking $booking): void
    {
        $testCenter = $booking->getTestCenter();
        $time = $booking->getTime();
        $isBooked = $this->getBookingRepository()->getBookingByTestCenterAndTime($testCenter, $time);

        if ($isBooked) {
            throw new BookingAlreadyExistsException($testCenter->getUuid()->toRfc4122(), $time);
        }
    }

    private function getBookingRepository(): BookingRepository
    {
        return $this->entityManager->getRepository(Booking::class);
    }
}
