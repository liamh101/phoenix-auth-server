<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'user:reset-password',
    description: 'Reset a user\'s password',
)]
class ResetUserPasswordCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User\'s email')
            ->addArgument('password', InputArgument::REQUIRED, 'New Password to replace the existing one')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('email');

        if (!is_string($email) || !is_string($password)) {
            $io->error('Email and password are required.');
            return Command::FAILURE;
        }

        $existingUser = $this->userRepository->findExistingAccount($email);

        if (!$existingUser) {
            $io->error('User ' . $email . ' Could Not Be Found');
            return Command::FAILURE;
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $existingUser,
            $password,
        );
        $existingUser->setPassword($hashedPassword);

        $this->userRepository->save($existingUser);

        $io->success('Password Reset');

        return Command::SUCCESS;
    }
}
