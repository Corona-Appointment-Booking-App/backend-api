<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Generator\OpeningTimeGeneratorInterface;
use App\Service\OpeningTimeServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-opening-time',
    description: 'Generate opening time e.g 8:00, 8:10, 09:00, 09:10',
)]
class GenerateOpeningTimeCommand extends Command
{
    private OpeningTimeGeneratorInterface $openingTimeGenerator;

    private OpeningTimeServiceInterface $openingTimeService;

    public function __construct(
        OpeningTimeGeneratorInterface $openingTimeGenerator,
        OpeningTimeServiceInterface $openingTimeService
    ) {
        $this->openingTimeGenerator = $openingTimeGenerator;
        $this->openingTimeService = $openingTimeService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $confirm = $io->ask('[Warning] this will delete all times do you want to continue?', 'y');
        if ('y' !== $confirm) {
            $io->info('not creating times answered with n (no).');

            return Command::SUCCESS;
        }

        $this->openingTimeService->deleteAllOpeningTimes();

        $generatedOpeningTimes = $this->openingTimeGenerator->generateOpeningTimes();
        foreach ($generatedOpeningTimes as $openingTime) {
            $time = $this->openingTimeService->createDateTimeFromTime($openingTime);
            $this->openingTimeService->createOpeningTime($time);
        }

        $io->success('opening times created');

        return Command::SUCCESS;
    }
}
