<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataTransferObject\BookingDto;
use App\DataTransferObject\BookingParticipantDto;
use App\Entity\TestCenter;
use App\Service\BookingServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    private BookingServiceInterface $bookingService;

    public function __construct(BookingServiceInterface $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var TestCenter $testCenter */
        $testCenter = $this->getReference(TestCenterFixtures::TEST_CENTER_REFERENCE);

        $bookingDto = new BookingDto();
        $bookingDto->setTestCenterId($testCenter->getUuid()->toRfc4122());
        $bookingDto->setBookingTime(new \DateTimeImmutable('03.01.2022 09:00'));
        $bookingDto->addParticipant((new BookingParticipantDto())->fromArray([
            'firstName' => 'Test',
            'lastName' => 'Lastname',
            'street' => 'Test Street',
            'houseNumber' => '50',
            'zipCode' => '012345',
            'city' => 'Test City',
            'phoneNumber' => '0123456',
            'email' => 'test@email.com',
            'birthDate' => new \DateTimeImmutable('01.01.1980'),
        ]));

        $this->bookingService->createBooking($bookingDto);
    }

    public function getDependencies()
    {
        return [
            TestCenterFixtures::class,
        ];
    }
}
