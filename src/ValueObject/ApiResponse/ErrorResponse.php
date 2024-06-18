<?php

namespace App\ValueObject\ApiResponse;

class ErrorResponse
{
    public function __construct(
      public readonly string $message,
    ) {
    }

}
