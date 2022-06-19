<?php

declare(strict_types=1);

namespace App\Service\Generator;

class OpeningTimeGenerator implements OpeningTimeGeneratorInterface
{
    public function generateOpeningTimes(): array
    {
        $openingTimes = [];

        foreach (range(5, 24) as $openingHour) {
            $openingHour = (string) $openingHour;

            if ('24' === $openingHour) {
                $openingHour = '00';
            }

            if (1 === mb_strlen($openingHour)) {
                $openingHour = '0'.$openingHour;
            }

            // $openingHour = $openingHour. ':00';
            $openingTimes[] = $openingHour;

            foreach (range(5, 59, 5) as $openingTime) {
                if (1 === mb_strlen((string) $openingTime)) {
                    $openingTime = '0'.$openingTime;
                }

                $openingTimes[] = sprintf('%s:%s', $openingHour, $openingTime);
            }
        }

        $openingTimes = array_map(function (string $openingTime) {
            if (2 === mb_strlen($openingTime)) {
                return $openingTime.':00';
            }

            return $openingTime;
        }, $openingTimes);

        return $openingTimes;
    }
}
