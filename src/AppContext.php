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

    public function __construct(
        string $contextYear,
        string $contextReleaseVersion
    ) {
        $this->contextYear = $contextYear;
        $this->contextReleaseVersion = $contextReleaseVersion;
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
}