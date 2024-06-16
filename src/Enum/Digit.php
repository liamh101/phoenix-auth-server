<?php

namespace App\Enum;

enum Digit: int
{
    case SIX = 6;
    case EIGHT = 8;

    public static function choiceValidation(): array
    {
        return [self::SIX->value, self::EIGHT->value];
    }
}
