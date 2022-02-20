<?php

declare(strict_types=1);

namespace App;

class AppContext
{
    /**
     * used to identify the year for processes
     * for example: booking, opening time generation
     */
    private string $contextYear;

    private string $contextReleaseVersion;

    private string $contextMailSender;

    public function __construct(
        string $contextYear,
        string $contextReleaseVersion,
        string $contextMailSender
    ) {
        $this->contextYear = $contextYear;
        $this->contextReleaseVersion = $contextReleaseVersion;
        $this->contextMailSender = $contextMailSender;
    }

    public function getContextYear(): string
    {
        return $this->contextYear;
    }

    public function setContextYear(string $contextYear): void
    {
        $this->contextYear = $contextYear;
    }

    public function getContextReleaseVersion(): string
    {
        return $this->contextReleaseVersion;
    }

    public function setContextReleaseVersion(string $contextReleaseVersion): void
    {
        $this->contextReleaseVersion = $contextReleaseVersion;
    }

    public function getContextMailSender(): string
    {
        return $this->contextMailSender;
    }

    public function setContextMailSender(string $contextMailSender): void
    {
        $this->contextMailSender = $contextMailSender;
    }
}