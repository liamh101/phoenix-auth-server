<?php

namespace App\Tests\Unit\Service;

use App\Entity\OtpRecord;
use App\Entity\User;
use App\Exception\UserException;
use App\Factory\UserFactory;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class UserServiceTest extends TestCase
{
    public function testGetCurrentlyLoggedInUser(): void
    {
        $user = new User();
        $user->setEmail('testGetCurrentlyLoggedInUser@test.com');

        $service = $this->makeService($user);

        self::assertSame('testGetCurrentlyLoggedInUser@test.com', $service->getCurrentUser()->getEmail());
    }

    /**
     * @return void
     *
     * @throws UserException
     */
    public function testGetCurrentUserNotLoggedIn(): void
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage('Fatal Error: Cannot find current authenticated User!');

        $service = $this->makeService(null);
        $service->getCurrentUser()->getEmail();
    }

    public function testAttachedLoggedInUserToRecord(): void
    {
        $user = new User();
        $user->setEmail('testAttachedLoggedInUserToRecord@test.com');

        $record = new OtpRecord();

        $service = $this->makeService($user);
        $service->attachCurrentUserToOtpRecord($record);

        self::assertInstanceOf(User::class, $record->user);
        self::assertSame('testAttachedLoggedInUserToRecord@test.com', $record->user->getEmail());
    }

    public function testTryToAttachUserToRecord(): void
    {
        $this->expectException(UserException::class);
        $this->expectExceptionMessage('Fatal Error: Cannot find current authenticated User!');

        $record = new OtpRecord();

        $service = $this->makeService(null);
        $service->attachCurrentUserToOtpRecord($record);
    }

    private function makeService(?User $user): UserService
    {
        $security = $this->createMock(Security::class);
        $security->expects($this->once())->method('getUser')->willReturn($user);

        return new UserService($security);
    }

}
