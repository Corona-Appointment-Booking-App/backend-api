<?php

declare(strict_types=1);

namespace App\Command;

use App\DataTransferObject\OpeningDayDto;
use App\Service\OpeningDayServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-opening-day',
    description: 'Create opening day e.g monday, tuesday, wednesday, thursday',
)]
class CreateOpeningDayCommand extends Command
{
    private OpeningDayServiceInterface $openingDayService;

    public function __construct(
        OpeningDayServiceInterface $openingDayService
    ) {
        $this->openingDayService = $openingDayService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $confirm = $io->ask('[Warning] this will delete all days do you want to continue?', 'y');
        if ('y' !== $confirm) {
            $io->info('not creating days answered with n (no).');

            return Command::SUCCESS;
        }

        $this->openingDayService->deleteAllOpeningDays();

        $openingDays = $this->getOpeningDays();

        foreach ($openingDays as $openingDay) {
            $this->openingDayService->createOpeningDay($openingDay);
        }

        $io->success('opening day created');

        return Command::SUCCESS;
    }

    private function getOpeningDays(): array
    {
        return [
            OpeningDayDto::DAY_MONDAY,
            OpeningDayDto::DAY_TUESDAY,
            OpeningDayDto::DAY_WEDNESDAY,
            OpeningDayDto::DAY_THURSDAY,
            OpeningDayDto::DAY_FRIDAY,
            OpeningDayDto::DAY_SATURDAY,
            OpeningDayDto::DAY_SUNDAY,
        ];
    }
}
