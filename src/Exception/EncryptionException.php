<?php

namespace App\Exception;

class EncryptionException extends \Exception
{
    public static function decryptionException(): self
    {
        return new self('Error decrypting secret');
    }

    public static function keyGenerationException(): self
    {
        return new self('Could not generate encryption key');
    }

    public static function ivException(): self
    {
        return new self('Error generating IV');
    }
}
