<?php

namespace App\Enum;

enum Step: int
{
    case THIRTY = 30;
    CASE SIXTY = 60;
    CASE NINETY = 90;
    CASE ONE_HUNDRED_AND_TWENTY = 120;

    public static function choiceValidation(): array
    {
        return [self::THIRTY->value, self::SIXTY->value, self::NINETY->value, self::ONE_HUNDRED_AND_TWENTY->value];
    }
}
