<?php

namespace App\EventListener;

use App\Entity\OtpRecord;
use App\Service\EncryptionService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: OtpRecord::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: OtpRecord::class)]
#[AsEntityListener(event: Events::postLoad, method: 'loadSecret', entity: OtpRecord::class)]
readonly class SecretEncryption
{
    public function __construct(
        private EncryptionService $encryptionService,
    ){
    }

    public function prePersist(OtpRecord $record, PrePersistEventArgs $event): void
    {
        $this->encryptSecret($record);
    }

    public function preUpdate(OtpRecord $record, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('secret')) {
            $this->encryptSecret($record);
        }
    }

    public function loadSecret(OtpRecord $record, PostLoadEventArgs $args): void
    {
        $record->secret = $this->encryptionService->decryptString($record->secret);
    }

    private function encryptSecret(OtpRecord $record): void
    {
        $record->secret = $this->encryptionService->encryptString($record->secret);
    }
}
