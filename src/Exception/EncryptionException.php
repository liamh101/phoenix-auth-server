<?php

namespace App\Exception;

class EncryptionException extends \Exception
{
    public static function DecryptionException(): self
    {
        return new self('Error decrypting secret');
    }

    public static function KeyGenerationException(): self
    {
        return new self('Could not generate encryption key');
    }

    public static function IvException(): self
    {
        return new self('Error generating IV');
    }
}
