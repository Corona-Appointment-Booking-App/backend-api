<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use App\AppConstants;
use App\DataTransferObject\BookingDto;
use App\DataTransferObject\BookingParticipantDto;

class BookingHydrator implements BookingHydratorInterface
{
    private const FALLBACK_BIRTHDATE = '01.01.1980';

    public function hydrateFromArray(array $payload): BookingDto
    {
        $bookingDto = new BookingDto();
        $bookingDto->setTestCenterId((string) $payload['selectedTestCenterId'] ??= '');

        $bookingTime = $this->createBookingTime($payload);
        $bookingDto->setBookingTime($bookingTime);

        foreach ((array) $payload['participants'] ??= [] as $participant) {
            $birthDate = $this->createBirthDate((string) $participant['birthDate'] ??= self::FALLBACK_BIRTHDATE);

            $bookingParticipantDto = (new BookingParticipantDto())->fromArray([
                'firstName' => (string) $participant['firstName'] ??= '',
                'lastName' => (string) $participant['lastName'] ??= '',
                'street' => (string) $participant['street'] ??= '',
                'houseNumber' => (string) $participant['houseNumber'] ??= '',
                'zipCode' => (string) $participant['zipCode'] ??= '',
                'city' => (string) $participant['city'] ??= '',
                'phoneNumber' => (string) $participant['phoneNumber'] ??= '',
                'email' => (string) $participant['email'] ??= '',
                'birthDate' => $birthDate,
            ]);

            $bookingDto->addParticipant($bookingParticipantDto);
        }

        return $bookingDto;
    }

    private function createBookingTime(array $payload): \DateTimeImmutable
    {
        $selectedOpeningDayDate = (string) $payload['selectedOpeningDayDate'] ??= '';
        $selectedOpeningTime = (string) $payload['selectedOpeningTime'] ??= '';

        return new \DateTimeImmutable(sprintf('%s %s', $selectedOpeningDayDate, $selectedOpeningTime));
    }

    private function createBirthDate(string $birthDate): \DateTimeImmutable
    {
        try {
            $date = (new \DateTimeImmutable())->createFromFormat(AppConstants::FORMAT_BIRTHDATE, $birthDate);

            if (!$date instanceof \DateTimeImmutable) {
                throw new \RuntimeException(sprintf('birthdate [%s] is invalid.', $birthDate));
            }

            return $date;
        } catch (\Throwable $e) {
            $date = (new \DateTimeImmutable())->createFromFormat(AppConstants::FORMAT_BIRTHDATE, self::FALLBACK_BIRTHDATE);

            if (!$date instanceof \DateTimeImmutable) {
                throw new \RuntimeException(sprintf('birthdate [%s] is invalid.', $birthDate));
            }

            return $date;
        }
    }
}
