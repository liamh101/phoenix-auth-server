<?php

namespace App\Enum;

enum Step: int
{
    case THIRTY = 30;
    case SIXTY = 60;
    case NINETY = 90;
    case ONE_HUNDRED_AND_TWENTY = 120;

    /**
     * @return int[]
     */
    public static function choiceValidation(): array
    {
        return [self::THIRTY->value, self::SIXTY->value, self::NINETY->value, self::ONE_HUNDRED_AND_TWENTY->value];
    }
}
