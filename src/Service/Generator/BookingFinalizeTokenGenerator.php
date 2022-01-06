<?php

declare(strict_types=1);

namespace App\Service\Generator;

use App\Exception\BookingFinalizeTokenGeneratorException;

class BookingFinalizeTokenGenerator implements BookingFinalizeTokenGeneratorInterface
{
    private string $appSecret;

    public function __construct(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function generateToken(
        array $participants,
        string $selectedOpeningDayDate,
        string $selectedOpeningTime,
        string $selectedTestCenterId
    ): string {
        $data = [
            'participants' => $participants,
            'selectedOpeningDayDate' => $selectedOpeningDayDate,
            'selectedOpeningTime' => $selectedOpeningTime,
            'selectedTestCenterId' => $selectedTestCenterId,
        ];

        if (empty($this->appSecret)) {
            throw new BookingFinalizeTokenGeneratorException('app secret is missing.');
        }

        try {
            $payload = json_encode($data, \JSON_THROW_ON_ERROR, 512);

            return hash_hmac('sha256', $payload, $this->appSecret);
        } catch (\Throwable $e) {
            throw new BookingFinalizeTokenGeneratorException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
