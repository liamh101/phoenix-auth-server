<?php

namespace App\Exception;

class UserException extends \Exception
{
    public static function cannotFindUser(): self
    {
        return new self('Fatal Error: Cannot find current authenticated User!');
    }
}
