<?php

namespace App\Tests\Unit\EventListener;

use App\Entity\OtpRecord;
use App\Entity\User;
use App\EventListener\UserAttachment;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class UserAttachmentTest extends TestCase
{
    public function testPrePersist(): void
    {
        $user = new User();
        $user->setEmail('testPrePersist@test.com');

        $record = new OtpRecord();
        $record->name = 'Hello World';
        $record->secret = '123456';
        $record->totpStep = 30;
        $record->otpDigits = 6;

        $security = $this->createMock(Security::class);
        $security->expects($this->once())->method('getUser')->willReturn($user);

        $userService = new UserService($security);
        $userAttachment = new UserAttachment($userService);

        $em = $this->createMock(EntityManager::class);

        $eventArgs = new PrePersistEventArgs($record, $em);

        $userAttachment->prePersist($record, $eventArgs);

        self::assertEquals('testPrePersist@test.com', $record->user->getEmail());
    }

}
