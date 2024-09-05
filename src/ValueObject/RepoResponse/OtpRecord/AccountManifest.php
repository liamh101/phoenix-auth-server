<?php

namespace App\ValueObject\RepoResponse\OtpRecord;

class AccountManifest
{
    public function __construct(
        public int $id,
        public \DateTimeInterface $updatedAt,
    ) {
    }

    /**
     * @return array<self>
     */
    public static function hydrateMany(array $data): array
    {
        $response = [];

        foreach ($data as $record) {
            $response[] = new AccountManifest(
                id: $record['id'],
                updatedAt: $record['updatedAt'],
            );
        }

        return $response;
    }

    /**
     * @return array<string,int|string>
     */
    public function formatResponse(): array
    {
        return [
            'id' => $this->id,
            'updatedAt' => (int)$this->updatedAt->format('U'),
        ];
    }
}
