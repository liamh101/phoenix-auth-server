<?php

namespace App\Tests\Integration\Command;

use App\Command\CreateUserCommand;
use App\Entity\OtpRecord;
use App\Factory\OtpRecordFactory;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CreateUserCommandTest extends IntegrationTestCase
{
    public function testCreateValidUser(): void
    {
        $command = $this->getCommand();
        $command->execute([
            'email' => 'testCreateValidUser@test.com',
            'password' => 'password',
        ]);

        $command->assertCommandIsSuccessful();

        $output = $command->getDisplay();
        $this->assertStringContainsString('User Successfully Created', $output);

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $user = $repo->findExistingAccount('testCreateValidUser@test.com');

        self::assertNotNull($user);
    }

    public function testCreateDuplicateUser(): void
    {
        $existingUser = UserFactory::createOne(['email' => 'testCreateDuplicateUser@test.com']);

        $command = $this->getCommand();
        $command->execute([
            'email' => 'testCreateDuplicateUser@test.com',
            'password' => 'password',
        ]);

        $command->assertCommandIsSuccessful();

        $output = $command->getDisplay();
        $this->assertStringContainsString('User testCreateDuplicateUser@test.com Already Exists.', $output);

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $user = $repo->findExistingAccount('testCreateDuplicateUser@test.com');

        self::assertEquals($existingUser->getId(), $user->getId());
    }

    public function testMutliUsers(): void
    {
        $existingUser = UserFactory::createOne(['email' => 'testMutliUsers@test.com']);

        $command = $this->getCommand();
        $command->execute([
            'email' => 'testMutliUsersTwo@test.com',
            'password' => 'password',
        ]);

        $command->assertCommandIsSuccessful();

        $output = $command->getDisplay();
        $this->assertStringContainsString('User Successfully Created', $output);

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $userOne = $repo->findExistingAccount('testMutliUsers@test.com');
        $userTwo = $repo->findExistingAccount('testMutliUsersTwo@test.com');

        self::assertEquals($existingUser->getId(), $userOne->getId());
        self::assertNotNull($userTwo->getId());
    }

    public function testRemoveOtherUsers(): void
    {
        $existingUser = UserFactory::createOne(['email' => 'testMutliUsers@test.com']);
        OtpRecordFactory::createOne(['user' => $existingUser]);

        $command = $this->getCommand();
        $command->execute([
            'email' => 'testMutliUsersTwo@test.com',
            'password' => 'password',
            '--remove-previous' => true,
        ]);

        $command->assertCommandIsSuccessful();

        $output = $command->getDisplay();
        $this->assertStringContainsString('User Successfully Created', $output);

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $userOne = $repo->findExistingAccount('testMutliUsers@test.com');
        $userTwo = $repo->findExistingAccount('testMutliUsersTwo@test.com');

        self::assertNull($userOne);
        self::assertNotNull($userTwo->getId());
    }

    private function getCommand(): CommandTester
    {
        return new CommandTester((new Application(self::$kernel))->find('user:create'));
    }
}
