<?php

namespace App\Command;

use App\Entity\User;
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
    name: 'user:create',
    description: 'Create a user account',
)]
class CreateUserCommand extends Command
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
            ->addArgument('email', InputArgument::REQUIRED, 'Email for User')
            ->addArgument('password', InputArgument::REQUIRED, 'User Password')
            ->addOption('multi-user', 'm', null, 'If enabled other users won\'t be removed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $multiUser = $input->getOption('multi-user');

        if (!is_string($email) || !is_string($password)) {
            $io->error('Email and password are required.');
            return Command::FAILURE;
        }

        $existingUser = $this->userRepository->findExistingAccount($email);

        if ($existingUser) {
            $io->warning('User ' . $email . ' Already Exists.');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password,
        );
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        if (!$multiUser) {
            $this->userRepository->deleteOtherUsers($user);
        }

        $io->success('User Successfully Created.');

        return Command::SUCCESS;
    }
}
