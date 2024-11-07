<?php

namespace App\Service;

use App\Entity\OtpRecord;
use App\Entity\User;
use App\Exception\UserException;
use Symfony\Bundle\SecurityBundle\Security;

readonly class UserService
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function getCurrentUser(): User
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            throw UserException::cannotFindUser();
        }

        return $currentUser;
    }

    /**
     * @throws UserException
     */
    public function attachCurrentUserToOtpRecord(OtpRecord $record): OtpRecord
    {
        $record->user = $this->getCurrentUser();

        return $record;
    }
}
