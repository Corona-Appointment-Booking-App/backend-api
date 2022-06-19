<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class CityLocationDto
{
    use ArrayAssignableTrait;

    private string $id;

    /**
     * @Assert\NotBlank
     */
    private string $cityId;

    /**
     * @Assert\NotBlank
     */
    private string $name;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCityId(): string
    {
        return $this->cityId;
    }

    public function setCityId(string $cityId): void
    {
        $this->cityId = $cityId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
