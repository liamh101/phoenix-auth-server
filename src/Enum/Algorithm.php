<?php

namespace App\Enum;

enum Algorithm: string
{
    case SHA1 = 'sha1';
    case SHA256 = 'sha256';
    case SHA512 = 'sha512';

    public static function choiceValidation(): array
    {
        return [null, self::SHA1->value, self::SHA256->value, self::SHA512->value];
    }
}
