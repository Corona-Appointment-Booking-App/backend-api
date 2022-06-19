<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class BookingParticipantDto
{
    use ArrayAssignableTrait;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $firstName;

    /**
     * @Assert\NotBlank
     */
    private string $lastName;

    /**
     * @Assert\NotBlank
     */
    private string $street;

    /**
     * @Assert\NotBlank
     */
    private string $houseNumber;

    /**
     * @Assert\NotBlank
     */
    private string $zipCode;

    /**
     * @Assert\NotBlank
     */
    private string $city;

    /**
     * @Assert\NotBlank
     */
    private string $phoneNumber;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    private string $email;

    private \DateTimeImmutable $birthDate;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}
