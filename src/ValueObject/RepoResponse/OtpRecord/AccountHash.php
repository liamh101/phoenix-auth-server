<?php

namespace App\ValueObject\RepoResponse\OtpRecord;

class AccountHash
{
    public function __construct(
        public int $id,
        public string $syncHash,
        public \DateTimeInterface $updatedAt,
    ) {
    }

    /**
     * @return array<string, int|string>
     */
    public function formatResponse(): array
    {
        return [
            'id' => $this->id,
            'syncHash' => $this->syncHash,
            'updatedAt' => (int)$this->updatedAt->format('U'),
        ];
    }
}
