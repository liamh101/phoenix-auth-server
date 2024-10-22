<?php

namespace App\Tests\Unit\EventListener;

use App\Entity\OtpRecord;
use App\EventListener\SecretEncryption;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\TestCase;

class SecretEncryptionTest extends TestCase
{
    public function testPrePersist(): void
    {
        $event = $this->makeEvent();

        $record = new OtpRecord();
        $record->name = 'Hello World';
        $record->secret = '123456';
        $record->totpStep = 30;
        $record->otpDigits = 6;

        $em = $this->createMock(EntityManager::class);

        $eventArgs = new PrePersistEventArgs($record, $em);

        $event->prePersist($record, $eventArgs);

        self::assertNotEquals('123456', $record->secret);
    }

    public function testPostPersistWithChanges(): void
    {
        $event = $this->makeEvent();

        $record = new OtpRecord();
        $record->name = 'Hello World';
        $record->secret = '123456';
        $record->totpStep = 30;
        $record->otpDigits = 6;

        $em = $this->createMock(EntityManager::class);

        $changed = ['secret' => ['12356']];

        $eventArgs = new PreUpdateEventArgs($record, $em, $changed);

        $event->preUpdate($record, $eventArgs);

        self::assertNotEquals('123456', $record->secret);
    }

    public function testPostPersistNoChanges(): void
    {
        $event = $this->makeEvent();

        $record = new OtpRecord();
        $record->name = 'Hello World';
        $record->secret = '123456';
        $record->totpStep = 30;
        $record->otpDigits = 6;

        $em = $this->createMock(EntityManager::class);

        $changed = [];

        $eventArgs = new PreUpdateEventArgs($record, $em, $changed);

        $event->preUpdate($record, $eventArgs);

        self::assertEquals('123456', $record->secret);
    }


    public function makeEvent(): SecretEncryption
    {
        return new SecretEncryption(new EncryptionService('12323435jsadf'));
    }
}
