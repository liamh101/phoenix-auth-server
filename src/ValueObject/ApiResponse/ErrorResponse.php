<?php

namespace App\ValueObject\ApiResponse;

use App\Entity\OtpRecord;

class ErrorResponse
{
    private const string NOT_FOUND_MESSAGE = '%s could not be found';

    public function __construct(
        public readonly string $message,
    ) {
    }

    public static function generateNotFoundErrorMessage(string $modelClass): string
    {
        return match ($modelClass) {
            OtpRecord::class => sprintf(self::NOT_FOUND_MESSAGE, 'Record'),
            default => sprintf(self::NOT_FOUND_MESSAGE, 'Model')
        };
    }
}
