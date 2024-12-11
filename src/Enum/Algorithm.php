<?php

namespace App\Enum;

enum Algorithm: string
{
    case SHA1 = 'SHA1';
    case SHA256 = 'SHA256';
    case SHA512 = 'SHA512';

    /**
     * @return null[]|string[]
     */
    public static function choiceValidation(): array
    {
        return [null, self::SHA1->value, self::SHA256->value, self::SHA512->value];
    }
}
