<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Generator;

use App\DataTransferObject\OpeningDayCollection;
use App\DataTransferObject\OpeningDayDto;
use App\Service\Generator\OpeningDayGeneratorText;
use App\Service\Generator\OpeningDayGeneratorTextInterface;
use PHPUnit\Framework\TestCase;

class OpeningDayGeneratorTextTest extends TestCase
{
    private OpeningDayGeneratorTextInterface $openingDayGeneratorText;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openingDayGeneratorText = new OpeningDayGeneratorText();
    }

    public function testGenerateOpeningDaysWithSixOpeningDaysAndTwoOpeningTimesForeachDayExceptSaturdayWithOneOpeningTime(): void
    {
        $availableDays = [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
        ];

        $openingDays = [];
        foreach ($availableDays as $availableDay) {
            if (OpeningDayDto::DAY_FRIDAY === $availableDay) {
                $openingDays[] = [
                    'day' => $availableDay,
                    'times' => [
                        [
                            'from' => '10:00',
                            'to' => '13:00',
                        ],
                        [
                            'from' => '15:00',
                            'to' => '18:00',
                        ],
                    ],
                ];
                continue;
            }

            if (OpeningDayDto::DAY_SATURDAY === $availableDay) {
                $openingDays[] = [
                    'day' => $availableDay,
                    'times' => [
                        [
                            'from' => '10:00',
                            'to' => '12:00',
                        ],
                    ],
                ];
                continue;
            }

            $openingDays[] = [
                'day' => $availableDay,
                'times' => [
                    [
                        'from' => '09:00',
                        'to' => '11:00',
                    ],
                    [
                        'from' => '16:00',
                        'to' => '18:00',
                    ],
                ],
            ];
        }

        $openingDayCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
        $generatedOpeningDaysText = $this->openingDayGeneratorText->generateOpeningDaysText($openingDayCollection);

        static::assertSame('Mo,Di,Mi,Do 09:00-11:00 Uhr 16:00-18:00 Uhr, Fr 10:00-13:00 Uhr 15:00-18:00 Uhr Sa 10:00-12:00 Uhr', $generatedOpeningDaysText);
    }

    public function testGenerateOpeningDaysWithSevenOpeningDaysAndOneOpeningTimesForeachDay(): void
    {
        $availableDays = [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
            OpeningDayDto::DAY_SUNDAY,
        ];

        $openingDays = [];
        foreach ($availableDays as $availableDay) {
            $openingDays[] = [
                'day' => $availableDay,
                'times' => [
                   [
                       'from' => '08:00',
                       'to' => '21:00',
                   ],
                ],
            ];
        }

        $openingDayCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
        $generatedOpeningDaysText = $this->openingDayGeneratorText->generateOpeningDaysText($openingDayCollection);

        static::assertSame('Mo,Di,Mi,Do,Fr,Sa,So 08:00-21:00 Uhr', $generatedOpeningDaysText);
    }

    public function testGenerateOpeningDaysWithSevenOpeningDaysAndOneOpeningTimesForeachDayAndSundayWithDifferentOpeningTimes(): void
    {
        $availableDays = [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
            OpeningDayDto::DAY_SUNDAY,
        ];

        $openingDays = [];
        foreach ($availableDays as $availableDay) {
            if (OpeningDayDto::DAY_SUNDAY === $availableDay) {
                $openingDays[] = [
                    'day' => $availableDay,
                    'times' => [
                        [
                            'from' => '10:00',
                            'to' => '16:00',
                        ],
                    ],
                ];
                continue;
            }

            $openingDays[] = [
                'day' => $availableDay,
                'times' => [
                    [
                        'from' => '07:00',
                        'to' => '18:00',
                    ],
                ],
            ];
        }

        $openingDayCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
        $generatedOpeningDaysText = $this->openingDayGeneratorText->generateOpeningDaysText($openingDayCollection);

        static::assertSame('Mo,Di,Mi,Do,Fr,Sa 07:00-18:00 Uhr So 10:00-16:00 Uhr', $generatedOpeningDaysText);
    }

    public function testGenerateOpeningDaysWithSevenOpeningDaysAndTwoOpeningTimeExceptWednesdayAndSaturdayWithOneOpeningTime(): void
    {
        $availableDays = [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
        ];

        $openingDays = [];
        foreach ($availableDays as $availableDay) {
            if (OpeningDayDto::DAY_WEDNESDAY === $availableDay || OpeningDayDto::DAY_SATURDAY === $availableDay) {
                $openingDays[] = [
                    'day' => $availableDay,
                    'times' => [
                        [
                            'from' => '09:00',
                            'to' => '12:15',
                        ],
                    ],
                ];
                continue;
            }

            $openingDays[] = [
                'day' => $availableDay,
                'times' => [
                    [
                        'from' => '09:00',
                        'to' => '12:15',
                    ],
                    [
                        'from' => '15:00',
                        'to' => '18:00',
                    ],
                ],
            ];
        }

        $openingDayCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
        $generatedOpeningDaysText = $this->openingDayGeneratorText->generateOpeningDaysText($openingDayCollection);

        static::assertSame('Mo,Di,Do,Fr 09:00-12:15 Uhr 15:00-18:00 Uhr, Mi 09:00-12:15 Uhr Sa 09:00-12:15 Uhr', $generatedOpeningDaysText);
    }

    public function testGenerateOpeningDaysWithTwoOpeningDaysAndOneOpeningTime(): void
    {
        $openingDays[] = [
            'day' => OpeningDayDto::DAY_MONDAY,
            'times' => [
                [
                    'from' => '06:00',
                    'to' => '06:30',
                ],
            ],
        ];
        $openingDays[] = [
            'day' => OpeningDayDto::DAY_TUESDAY,
            'times' => [
                [
                    'from' => '07:00',
                    'to' => '07:10',
                ],
            ],
        ];

        $openingDayCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
        $generatedOpeningDaysText = $this->openingDayGeneratorText->generateOpeningDaysText($openingDayCollection);

        static::assertSame('Mo 06:00-06:30 Uhr Di 07:00-07:10 Uhr', $generatedOpeningDaysText);
    }
}
