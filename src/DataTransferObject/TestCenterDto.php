<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class TestCenterDto
{
    use ArrayAssignableTrait;

    private string $id;

    /**
     * @Assert\NotBlank
     */
    private string $cityLocationId;

    /**
     * @Assert\NotBlank
     */
    private string $name;

    /**
     * @Assert\NotBlank
     */
    private string $address;

    /**
     * @Assert\NotBlank
     */
    private array $openingDays;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCityLocationId(): string
    {
        return $this->cityLocationId;
    }

    public function setCityLocationId(string $cityLocationId): void
    {
        $this->cityLocationId = $cityLocationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getOpeningDays(): array
    {
        return $this->openingDays;
    }

    public function setOpeningDays(array $openingDays): void
    {
        $this->openingDays = $openingDays;
    }
}
