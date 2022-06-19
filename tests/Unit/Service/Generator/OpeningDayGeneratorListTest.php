<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Generator;

use App\DataTransferObject\OpeningDayCollection;
use App\DataTransferObject\OpeningDayGeneratorListCollection;
use App\DataTransferObject\OpeningDayGeneratorListDto;
use App\Service\Generator\OpeningDayGeneratorList;
use App\Service\Generator\OpeningDayGeneratorListInterface;
use App\Service\OpeningDayService;
use PHPUnit\Framework\TestCase;

class OpeningDayGeneratorListTest extends TestCase
{
    private OpeningDayGeneratorListInterface $openingDayGeneratorList;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openingDayGeneratorList = new OpeningDayGeneratorList($this->createMock(OpeningDayService::class));
    }

    public function testGenerateOpeningDaysForDayStartingFromFriday(): void
    {
        // todo add more test cases starting from monday, tuesday, etc..

        $openingDays = $this->getOpeningDays();

        $openingDayCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
        $generated = $this->openingDayGeneratorList->generateOpeningDaysForDays($openingDayCollection, '29.10.2021');
        static::assertInstanceOf(OpeningDayGeneratorListCollection::class, $generated);
        static::assertCount(7, $generated->getOpeningDays());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'friday',
            'date' => (new \DateTimeImmutable('friday'))->format('d.m.Y'),
            'openingTimeFrom' => '20:10',
            'openingTimeTo' => '20:40',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[0]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[0]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[0]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[0]->isBookedOut());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'saturday',
            'date' => (new \DateTimeImmutable('saturday'))->format('d.m.Y'),
            'openingTimeFrom' => '13:10',
            'openingTimeTo' => '17:40',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[1]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[1]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[1]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[1]->isBookedOut());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'sunday',
            'date' => (new \DateTimeImmutable('sunday'))->format('d.m.Y'),
            'openingTimeFrom' => '12:00',
            'openingTimeTo' => '23:45',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[2]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[2]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[2]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[2]->isBookedOut());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'monday',
            'date' => (new \DateTimeImmutable('monday'))->format('d.m.Y'),
            'openingTimeFrom' => '8:10',
            'openingTimeTo' => '11:20',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[3]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[3]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[3]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[3]->isBookedOut());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'tuesday',
            'date' => (new \DateTimeImmutable('tuesday'))->format('d.m.Y'),
            'openingTimeFrom' => '18:20',
            'openingTimeTo' => '19:15',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[4]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[4]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[4]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[4]->isBookedOut());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'friday',
            'date' => (new \DateTimeImmutable('friday'))->format('d.m.Y'),
            'openingTimeFrom' => '20:10',
            'openingTimeTo' => '20:40',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[5]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[5]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[5]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[5]->isBookedOut());

        $expectedOpeningDay = (new OpeningDayGeneratorListDto())->fromArray([
            'day' => 'saturday',
            'date' => (new \DateTimeImmutable('saturday'))->format('d.m.Y'),
            'openingTimeFrom' => '13:10',
            'openingTimeTo' => '17:40',
            'isBookedOut' => false,
        ]);

        static::assertSame($expectedOpeningDay->getDay(), $generated->getOpeningDays()[6]->getDay());
        static::assertSame($expectedOpeningDay->getOpeningTimeFrom(), $generated->getOpeningDays()[6]->getOpeningTimeFrom());
        static::assertSame($expectedOpeningDay->getOpeningTimeTo(), $generated->getOpeningDays()[6]->getOpeningTimeTo());
        static::assertFalse($generated->getOpeningDays()[6]->isBookedOut());
    }

    private function getOpeningDays(): array
    {
        return [
            [
                'day' => 'monday',
                'times' => [
                    [
                        'from' => '8:10',
                        'to' => '11:20',
                    ],
                ],
            ],
            [
                'day' => 'tuesday',
                'times' => [
                    [
                        'from' => '18:20',
                        'to' => '19:15',
                    ],
                ],
            ],
            [
                'day' => 'friday',
                'times' => [
                    [
                        'from' => '20:10',
                        'to' => '20:40',
                    ],
                ],
            ],
            [
                'day' => 'saturday',
                'times' => [
                    [
                        'from' => '13:10',
                        'to' => '17:40',
                    ],
                ],
            ],
            [
                'day' => 'sunday',
                'times' => [
                    [
                        'from' => '12:00',
                        'to' => '14:40',
                    ],
                    [
                        'from' => '15:00',
                        'to' => '21:26',
                    ],
                    [
                        'from' => '22:00',
                        'to' => '23:45',
                    ],
                ],
            ],
        ];
    }
}
