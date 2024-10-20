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
     * @param array<int, array<string, int|\DateTimeInterface>> $data
     * @return array<int, self>
     */
    public static function hydrateMany(array $data): array
    {
        $response = [];

        foreach ($data as $record) {
            if (!is_int($record['id']) || !$record['updatedAt'] instanceof \DateTimeInterface) {
                throw new \RuntimeException('Invalid Data Provided');
            }

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
