<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class OpeningTimeDto extends AbstractDto
{
    private string $id;

    /**
     * @Assert\NotBlank
     */
    private string $from;

    /**
     * @Assert\NotBlank
     */
    private string $to;

    private OpeningDayDto $openingDay;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    public function getOpeningDay(): OpeningDayDto
    {
        return $this->openingDay;
    }

    public function setOpeningDay(OpeningDayDto $openingDay): void
    {
        $this->openingDay = $openingDay;
    }
}
