<?php

namespace App\Tests\Unit\Service;

use App\Entity\OtpRecord;
use App\Service\RecordService;
use PHPUnit\Framework\TestCase;

class RecordServiceTest extends TestCase
{

    /**
     * @dataProvider recordHashProvider
     */
    public function testHashGeneration(string $name, string $secret, int $step, int $digits, ?string $algorithm, string $expectedHash): void
    {
        $service = $this->makeService();
        $record = new OtpRecord();
        $record->name = $name;
        $record->secret = $secret;
        $record->totpStep = $step;
        $record->otpDigits = $digits;
        $record->totpAlgorithm = $algorithm;

        $hash = $service->generateRecordHash($record);

        self::assertEquals($expectedHash, $hash);
    }

    private function recordHashProvider(): array
    {
        return [
            [
                'Hello World',
                '123456',
                30,
                6,
                null,
                'e9f8a5233c77f52a3312bebd32009cba9847e045c578e20a75c47808d551a2ca1cadb9281054ac0490c507dc53d4b4f3d1a9be0275a103f5616765c6b708bf7b',
            ],
            [
                'Hello World',
                '123456',
                30,
                6,
                'sha512',
                '6224f692e6e69593f5eb7ef0ced948652e8c95f283c73858bd3d3554fe80a0f9efcd417c85d60749e0a0b5d3e4aa3560874de3ff1c8c98e62400a1f5e32dad5a',
            ],
            [
                'Hello World 2',
                '123456',
                30,
                6,
                null,
                '65c598b1d3cc64b2fbebc8bbc57773ec20f58d10f6736c36bc66c6f434a85625d9dbb324b407af03c96ed062f40b894980ba8c2da348cb859c3bc4d8fa2226e7',
            ],
            [
                'Hello World',
                '1234567890',
                30,
                6,
                null,
                'd9488b6209cc4700da90511eb6d9b104a86695a5ca86e527f1666457559d5409b00bd9d9a46e6bb74e2d626b65ff72abadd72ec2f14aff1b3b09dc24d0c7b11b',
            ],
            [
                'Hello World',
                '123456',
                60,
                6,
                null,
                '777c5d1aba7553fe819d6c37c342dc1f46a657ac52f34a2a35822b2b22aa66317d7a3bf1895c8b7eaf58aeeab750b8fe9a0b31e69a98c2ab923fa83f81a3fc50',
            ],
            [
                'Hello World',
                '123456',
                30,
                8,
                null,
                '2d13b6e4b552bbb2f499130cbb79d6e4eb8317bc96d71ff91b252f8e872ed4fb0841fe7ea7c9be3cce520bc670cdef11be3a53c8a3dc4c858b9eb0fdb845b11b',
            ],
        ];
    }

    public function testUpdateExistingRecord(): void
    {
        $service = $this->makeService();

        $oldRecord = new OtpRecord();
        $oldRecord->id = 1;
        $oldRecord->name = 'Hello World';
        $oldRecord->secret = '123456';
        $oldRecord->totpStep = 30;
        $oldRecord->otpDigits = 6;
        $oldRecord->totpAlgorithm = null;
        $oldRecord->createdAt = new \DateTimeImmutable('yesterday');
        $oldRecord->updatedAt = new \DateTime('now');

        $newRecord = new OtpRecord();
        $newRecord->name = 'Hello World 2';
        $newRecord->secret = '12345678';
        $newRecord->totpStep = 60;
        $newRecord->otpDigits = 8;
        $newRecord->totpAlgorithm = 'SHA512';

        $updatedRecord = $service->updateExistingRecord($oldRecord, $newRecord);

        self::assertEquals($oldRecord->id, $updatedRecord->id);
        self::assertEquals($newRecord->name, $updatedRecord->name);
        self::assertEquals($newRecord->secret, $updatedRecord->secret);
        self::assertEquals($newRecord->totpStep, $updatedRecord->totpStep);
        self::assertEquals($newRecord->otpDigits, $updatedRecord->otpDigits);
        self::assertEquals($newRecord->totpAlgorithm, $updatedRecord->totpAlgorithm);
        self::assertEquals($oldRecord->createdAt, $updatedRecord->createdAt);
        self::assertEquals($oldRecord->updatedAt, $updatedRecord->updatedAt);
    }


    private function makeService(): RecordService
    {
        return new RecordService();
    }
}
