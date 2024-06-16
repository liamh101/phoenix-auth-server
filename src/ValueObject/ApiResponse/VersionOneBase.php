<?php

namespace App\ValueObject\ApiResponse;

class VersionOneBase
{
    public readonly int $version;

    public function __construct(
        public object|array $data,
    ) {
        $this->version = 1;
    }
}
