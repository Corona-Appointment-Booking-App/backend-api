<?php

declare(strict_types=1);

namespace App\Command;

use App\DataTransferObject\UserDto;
use App\Service\UserServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create admin with email and password',
)]
class CreateAdminCommand extends Command
{
    private UserServiceInterface $userService;

    public function __construct(
        UserServiceInterface $userService
    ) {
        $this->userService = $userService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userDto = (new UserDto())->fromArray([
            'email' => $input->getArgument('email'),
            'password' => $input->getArgument('password'),
        ]);

        if (empty($userDto->getEmail())) {
            $io->error('email is missing.');

            return Command::FAILURE;
        }

        if (empty($userDto->getPassword())) {
            $io->error('password is missing.');

            return Command::FAILURE;
        }

        try {
            $this->userService->createUser($userDto);

            $io->success('admin created');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
