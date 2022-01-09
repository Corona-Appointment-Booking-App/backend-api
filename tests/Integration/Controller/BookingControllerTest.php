<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\DataTransferObject\CityDto;
use App\DataTransferObject\CityLocationDto;
use App\DataTransferObject\OpeningDayDto;
use App\DataTransferObject\TestCenterDto;
use App\Entity\Booking;
use App\Entity\BookingParticipant;
use App\Entity\City;
use App\Entity\CityLocation;
use App\Entity\OpeningDay;
use App\Entity\OpeningTime;
use App\Entity\TestCenter;
use App\Service\CityLocationServiceInterface;
use App\Service\CityServiceInterface;
use App\Service\Generator\OpeningTimeGeneratorInterface;
use App\Service\OpeningDayServiceInterface;
use App\Service\OpeningTimeServiceInterface;
use App\Service\TestCenterServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    private const SLUG_TEST_CENTER = 'testtestcenter';
    private const SLUG_CITY_LOCATION = 'testcitylocation';

    private KernelBrowser $client;

    private CityServiceInterface $cityService;

    private CityLocationServiceInterface $cityLocationService;

    private TestCenterServiceInterface $testCenterService;

    private OpeningDayServiceInterface $openingDayService;

    private OpeningTimeServiceInterface $openingTimeService;

    private OpeningTimeGeneratorInterface $openingTimeGenerator;

    private ?EntityManagerInterface $entityManager;

    private TestCenter $testCenter;

    private CityLocation $cityLocation;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->cityService = static::getContainer()->get(CityServiceInterface::class);
        $this->cityLocationService = static::getContainer()->get(CityLocationServiceInterface::class);
        $this->testCenterService = static::getContainer()->get(TestCenterServiceInterface::class);
        $this->openingDayService = static::getContainer()->get(OpeningDayServiceInterface::class);
        $this->openingTimeService = static::getContainer()->get(OpeningTimeServiceInterface::class);
        $this->openingTimeGenerator = static::getContainer()->get(OpeningTimeGeneratorInterface::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->createOpeningDays();
        $this->createOpeningTimes();

        $this->createCity();
        $this->createCityLocation();
        $this->createTestCenter();

        $this->testCenter = $this->testCenterService->getTestCenterBySeoSlug(static::SLUG_TEST_CENTER);
        $this->cityLocation = $this->cityLocationService->getCityLocationBySeoSlug(static::SLUG_CITY_LOCATION);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->truncateEntities([
            BookingParticipant::class,
            Booking::class,
            OpeningDay::class,
            OpeningTime::class,
            TestCenter::class,
            CityLocation::class,
            City::class,
        ]);

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testBookingCheckout(): void
    {
        $requestData = [
            'testCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
        ];

        $this->client->request(
            'POST',
            '/api/booking/checkout',
            [],
            [],
            [],
            json_encode($requestData)
        );

        static::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertArrayHasKey('success', $response);
        static::assertArrayHasKey('data', $response);
        static::assertTrue($response['success']);

        static::assertArrayHasKey('testCenter', $response['data']);
        static::assertArrayHasKey('availableOpeningTimes', $response['data']);

        $testCenter = $response['data']['testCenter'];

        static::assertArrayHasKey('id', $testCenter);
        static::assertArrayHasKey('name', $testCenter);
        static::assertArrayHasKey('address', $testCenter);
        static::assertArrayHasKey('seoSlug', $testCenter);
        static::assertArrayHasKey('cityLocation', $testCenter);

        static::assertSame($this->testCenter->getUuid()->toRfc4122(), $testCenter['id']);
        static::assertSame('testTestCenter', $testCenter['name']);
        static::assertSame('testAddress', $testCenter['address']);
        static::assertSame('testtestcenter', $testCenter['seoSlug']);

        static::assertArrayHasKey('id', $testCenter['cityLocation']);
        static::assertArrayHasKey('name', $testCenter['cityLocation']);
        static::assertArrayHasKey('seoSlug', $testCenter['cityLocation']);

        static::assertSame($this->cityLocation->getUuid()->toRfc4122(), $testCenter['cityLocation']['id']);
        static::assertSame('testCityLocation', $testCenter['cityLocation']['name']);
        static::assertSame('testcitylocation', $testCenter['cityLocation']['seoSlug']);

        static::assertCount(25, $response['data']['availableOpeningTimes']);
    }

    public function testBookingFinalize(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
            'selectedOpeningTime' => '12:00',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        static::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertArrayHasKey('success', $response);
        static::assertTrue($response['success']);
    }

    public function testBookingFinalizeWithMoreThanFiveParticipants(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
            'selectedOpeningTime' => '09:00',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ],
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ],
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ],
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ],
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ],
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(400);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_VALIDATION', $response['error']);
        static::assertFalse($response['success']);
    }

    public function testBookingFinalizeWithBeforeOpeningTime(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
            'selectedOpeningTime' => '09:00',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(400);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_BOOKING_NOT_ALLOWED', $response['error']);
        static::assertFalse($response['success']);
    }

    public function testBookingFinalizeWithAfterOpeningTime(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
            'selectedOpeningTime' => '12:01',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(400);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_BOOKING_NOT_ALLOWED', $response['error']);
        static::assertFalse($response['success']);
    }

    public function testBookingFinalizeWithYearNotEqualsFromYearInAppContext(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '07.12.2020',
            'selectedOpeningTime' => '12:00',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(400);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_BOOKING_NOT_ALLOWED', $response['error']);
        static::assertFalse($response['success']);
    }

    public function testBookingFinalizeWithInvalidToken(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
            'selectedOpeningTime' => '12:00',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = '';

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(500);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_UNEXPECTED_ERROR', $response['error']);
        static::assertFalse($response['success']);
    }

    public function testBookingFinalizeWithOpeningDayDateNotMonday(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '11.01.2022',
            'selectedOpeningTime' => '12:00',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(400);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_BOOKING_NOT_ALLOWED', $response['error']);
        static::assertFalse($response['success']);
    }

    public function testBookingFinalizeWithAlreadyExistingTime(): void
    {
        $requestData = [
            'selectedTestCenterId' => $this->testCenter->getUuid()->toRfc4122(),
            'selectedOpeningDayDate' => '10.01.2022',
            'selectedOpeningTime' => '11:05',
            'participants' => [
                [
                    'firstName' => 'test',
                    'lastName' => 'developer',
                    'street' => 'testStreet',
                    'houseNumber' => '50',
                    'zipCode' => '012345',
                    'city' => 'testCity',
                    'phoneNumber' => '0123456789',
                    'email' => 'test@email.com',
                    'birthDate' => '01.01.1980'
                ]
            ],
        ];
        $requestData['token'] = $this->getTokenFromRequestData($requestData);

        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );
        $this->client->request(
            'POST',
            '/api/booking/checkout/finalize',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertResponseStatusCodeSame(400);
        static::assertArrayHasKey('error', $response);
        static::assertArrayHasKey('success', $response);

        static::assertSame('ERROR_BOOKING_EXISTS', $response['error']);
        static::assertFalse($response['success']);
    }

    private function createOpeningDays(): void
    {
        $generatedOpeningTimes = $this->openingTimeGenerator->generateOpeningTimes();
        foreach ($generatedOpeningTimes as $openingTime) {
            $time = $this->openingTimeService->createDateTimeFromTime($openingTime);
            $this->openingTimeService->createOpeningTime($time);
        }
    }

    private function createOpeningTimes(): void
    {
        $openingDays = [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
            OpeningDayDto::DAY_SUNDAY,
        ];

        foreach ($openingDays as $openingDay) {
            $this->openingDayService->createOpeningDay($openingDay);
        }
    }

    private function createCity(): City
    {
        $cityDto = new CityDto();
        $cityDto->setName('testCity');

        return $this->cityService->createCity($cityDto);
    }

    private function createCityLocation(): CityLocation
    {
        $city = $this->cityService->getCityBySeoSlug('testCity');

        $cityLocationDto = new CityLocationDto();
        $cityLocationDto->setCityId($city->getUuid()->toRfc4122());
        $cityLocationDto->setName('testCityLocation');

        return $this->cityLocationService->createCityLocation($cityLocationDto);
    }

    private function createTestCenter(): TestCenter
    {
        $cityLocation = $this->cityLocationService->getCityLocationBySeoSlug('testCityLocation');

        $testCenterDto = new TestCenterDto();
        $testCenterDto->setCityLocationId($cityLocation->getUuid()->toRfc4122());
        $testCenterDto->setName('testTestCenter');
        $testCenterDto->setAddress('testAddress');
        $testCenterDto->setOpeningDays(
            [
                [
                    'day' => OpeningDayDto::DAY_MONDAY,
                    'times' => [
                        [
                            'from' => '10:00',
                            'to' => '12:00',
                        ]
                    ]
                ]
            ]
        );

        return $this->testCenterService->createTestCenter($testCenterDto);
    }

    private function getTokenFromRequestData(array $requestData): string
    {
        $this->client->request(
            'POST',
            '/api/booking/checkout/fetch-token',
            [],
            [],
            [],
            json_encode($requestData)
        );

        static::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        static::assertArrayHasKey('success', $response);
        static::assertArrayHasKey('token', $response);

        static::assertTrue($response['success']);
        static::assertNotEmpty($response['token']);

        return $response['token'];
    }

    private function truncateEntities(array $entities): void
    {
        $connection = $this->entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $classMetadata = $this->entityManager->getClassMetadata($entity);

            $query = $databasePlatform->getTruncateTableSQL($classMetadata->getTableName());
            $connection->executeQuery($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
