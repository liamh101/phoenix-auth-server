<?php

namespace App\Tests\Integration\Command;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetUserPasswordCommandTest extends IntegrationTestCase
{
    public function testResetUserPassword(): void
    {
        $existingUser = UserFactory::createOne(['email' => 'testResetUserPassword@test.com']);
        $oldPasswordHash = $existingUser->getPassword();

        $existingUserTwo = UserFactory::createOne(['email' => 'testResetUserPassword2@test.com']);

        $command = $this->getCommand();
        $command->execute([
            'email' => 'testResetUserPassword@test.com',
            'password' => 'password',
        ]);

        $command->assertCommandIsSuccessful();
        $output = $command->getDisplay();
        $this->assertStringContainsString('Password Reset', $output);

        /** @var UserRepository $repo */
        $repo = self::getContainer()->get(UserRepository::class);
        $userOne = $repo->findExistingAccount('testResetUserPassword@test.com');
        $userTwo = $repo->findExistingAccount('testResetUserPassword2@test.com');

        self::assertNotSame($oldPasswordHash, $userOne->getPassword());
        self::assertSame($existingUserTwo->getPassword(), $userTwo->getPassword());
    }

    public function testResetPasswordNonExistentUser(): void
    {
        $command = $this->getCommand();
        $command->execute([
            'email' => 'testResetPasswordNonExistentUser@test.com',
            'password' => 'password',
        ]);

        self::assertSame(1, $command->getStatusCode());
        $output = $command->getDisplay();
        $this->assertStringContainsString('User testResetPasswordNonExistentUser@test.com Could Not Be Found', $output);
    }

    public function testNoPasswordProvided(): void
    {
        $this->expectException(\RuntimeException::class);

        $command = $this->getCommand();
        $command->execute([
            'email' => 'testResetPasswordNonExistentUser@test.com',
        ]);
    }

    private function getCommand(): CommandTester
    {
        return new CommandTester((new Application(self::$kernel))->find('user:reset-password'));
    }
}
