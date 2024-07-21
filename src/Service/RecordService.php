<?php

namespace App\Service;

use App\Entity\OtpRecord;

readonly class RecordService
{
    private const string HASH_ALGORITHM = 'sha512';

    public function generateRecordHash(OtpRecord $record): string
    {
        return hash(self::HASH_ALGORITHM, $record->name . $record->secret . $record->totpStep . $record->otpDigits . $record->totpAlgorithm);
    }

}
