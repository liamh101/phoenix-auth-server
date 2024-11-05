<?php

namespace App\EventListener;

use App\Entity\OtpRecord;
use App\Exception\UserException;
use App\Service\UserService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: OtpRecord::class)]
readonly class UserAttachment
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    /**
     * @throws UserException
     */
    public function prePersist(OtpRecord $record, LifecycleEventArgs $args): void
    {
        if (isset($record->user)) {
            return;
        }

        $this->userService->attachCurrentUserToOtpRecord($record);
    }

}
