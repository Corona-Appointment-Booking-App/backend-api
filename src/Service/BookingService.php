<?php

declare(strict_types=1);

namespace App\Service;

use App\AppConstants;
use App\AppContext;
use App\DataTransferObject\BookingDto;
use App\Entity\Booking;
use App\Entity\BookingParticipant;
use App\Entity\TestCenter;
use App\Event\BookingCreatedEvent;
use App\Exception\BookingAlreadyExistsException;
use App\Exception\BookingNotAllowedException;
use App\Exception\BookingNotFoundException;
use App\Repository\BookingRepository;
use App\Repository\Result\PaginatedItemsResult;
use App\Service\Util\SanitizerInterface;
use App\Service\Validator\BookingValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookingService implements BookingServiceInterface
{
    private EntityManagerInterface $entityManager;

    private EventDispatcherInterface $eventDispatcher;

    private MailerInterface $mailer;

    private TestCenterServiceInterface $testCenterService;

    private OpeningTimeServiceInterface $openingTimeService;

    private BookingValidatorInterface $bookingValidator;

    private SanitizerInterface $htmlSanitizer;

    private AppContext $appContext;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        MailerInterface $mailer,
        TestCenterServiceInterface $testCenterService,
        OpeningTimeServiceInterface $openingTimeService,
        BookingValidatorInterface $bookingValidator,
        SanitizerInterface $htmlSanitizer,
        AppContext $appContext
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
        $this->testCenterService = $testCenterService;
        $this->openingTimeService = $openingTimeService;
        $this->bookingValidator = $bookingValidator;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->appContext = $appContext;
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

        $this->eventDispatcher->dispatch(new BookingCreatedEvent($booking), BookingCreatedEvent::NAME);

        return $booking;
    }

    public function sendEmailConfirmation(Booking $booking): void
    {
        $toAddresses = [];
        foreach ($booking->getParticipants() as $participant) {
            /** @var BookingParticipant $participant */
            $toAddresses[] = new Address($participant->getEmail());
        }

        $email = (new TemplatedEmail())
            ->from($this->appContext->getContextMailSender())
            ->to(...$toAddresses)
            ->subject('Buchungsbestätigung')
            ->htmlTemplate('emails/booking-confirmation.html.twig')
            ->context([
                'testCenter' => [
                    'name' => $booking->getTestCenter()->getName(),
                    'address' => $booking->getTestCenter()->getAddress()
                ],
                'bookingCode' => mb_substr($booking->getUuid()->toBase32(), 0, 10),
                'bookingDate' => $booking->getTime()->format(AppConstants::FORMAT_EMAIL_CONFIRMATION),
                'cancelUrl' => 'todo'
            ]);

        $this->mailer->send($email);
    }

    private function validateBookingTimeIsValid(Booking $booking): void
    {
        $bookingDay = mb_strtolower($booking->getTime()->format(AppConstants::FORMAT_DAY));
        $bookingTime = $booking->getTime()->format(AppConstants::FORMAT_TIME);
        $openingTimesForDay = $this->openingTimeService->getOpeningTimesForDay($bookingDay, $booking->getTestCenter()->getOpeningDays());
        $testCenterId = $booking->getTestCenter()->getUuid()->toRfc4122();

        if ($booking->getTime()->format(AppConstants::FORMAT_YEAR) !== $this->appContext->getContextYear()) {
            throw new BookingNotAllowedException($testCenterId, $booking->getTime());
        }

        if (empty($openingTimesForDay)) {
            throw new BookingNotAllowedException($testCenterId, $booking->getTime());
        }

        $allowedOpeningTimes = $this->getAllowedOpeningTimes($openingTimesForDay);

        if (!\in_array($bookingTime, $allowedOpeningTimes, true)) {
            throw new BookingNotAllowedException($testCenterId, $booking->getTime());
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

    private function getAllowedOpeningTimes(array $openingTimesForDay): array
    {
        $allowedOpeningTimes = [];

        foreach ($openingTimesForDay as $openingTimeForDay) {
            try {
                $openingTime = $this->openingTimeService->getOpeningTimeByTime($openingTimeForDay->getTime());
                $allowedOpeningTimes[] = $openingTime->getTime()->format(AppConstants::FORMAT_TIME);
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $allowedOpeningTimes;
    }

    private function getBookingRepository(): BookingRepository
    {
        return $this->entityManager->getRepository(Booking::class);
    }
}
