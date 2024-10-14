<?php

namespace App\Tests\Integration\Repository;

use App\Factory\OtpRecordFactory;
use App\Repository\OtpRecordRepository;
use App\Service\EncryptionService;
use App\Tests\Integration\IntegrationTestCase;

class OtpRecordRepositoryTest extends IntegrationTestCase
{

    public function testGetAllRecords(): void
    {
        $repo = $this->getRepository();

        $third = OtpRecordFactory::new()->create();
        $second = OtpRecordFactory::new()->create();
        $first = OtpRecordFactory::new()->create();

        $result = $repo->getAll();

        self::assertCount(3, $result);
        self::assertEquals($first->id, $result[0]->id);
        self::assertEquals($first->name, $result[0]->name);
        self::assertEquals($first->secret, $result[0]->secret);
        self::assertEquals($first->otpDigits, $result[0]->otpDigits);
        self::assertEquals($first->totpAlgorithm, $result[0]->totpAlgorithm);
        self::assertEquals($first->syncHash, $result[0]->syncHash);
        self::assertEquals($first->createdAt, $result[0]->createdAt);
        self::assertEquals($first->updatedAt, $result[0]->updatedAt);

        self::assertEquals($second->id, $result[1]->id);
        self::assertEquals($second->name, $result[1]->name);
        self::assertEquals($second->secret, $result[1]->secret);
        self::assertEquals($second->otpDigits, $result[1]->otpDigits);
        self::assertEquals($second->totpAlgorithm, $result[1]->totpAlgorithm);
        self::assertEquals($second->syncHash, $result[1]->syncHash);
        self::assertEquals($second->createdAt, $result[1]->createdAt);
        self::assertEquals($second->updatedAt, $result[1]->updatedAt);

        self::assertEquals($third->id, $result[2]->id);
        self::assertEquals($third->name, $result[2]->name);
        self::assertEquals($third->secret, $result[2]->secret);
        self::assertEquals($third->otpDigits, $result[2]->otpDigits);
        self::assertEquals($third->totpAlgorithm, $result[2]->totpAlgorithm);
        self::assertEquals($third->syncHash, $result[2]->syncHash);
        self::assertEquals($third->createdAt, $result[2]->createdAt);
        self::assertEquals($third->updatedAt, $result[2]->updatedAt);
    }

    public function testGetManifest(): void
    {
        $repo = $this->getRepository();

        $first = OtpRecordFactory::createOne();
        $second = OtpRecordFactory::createOne();
        $third = OtpRecordFactory::createOne();

        $result = $repo->getAccountManifest();

        self::assertCount(3, $result);

        self::assertEquals($first->id, $result[0]->id);
        self::assertEqualsWithDelta($first->updatedAt, $result[0]->updatedAt, 1);

        self::assertEquals($second->id, $result[1]->id);
        self::assertEqualsWithDelta($second->updatedAt, $result[1]->updatedAt, 1);

        self::assertEquals($third->id, $result[2]->id);
        self::assertEqualsWithDelta($third->updatedAt, $result[2]->updatedAt, 1);
    }

    public function testGetSingleHashFound(): void
    {
        $repo = $this->getRepository();

        $record = OtpRecordFactory::createOne();
        OtpRecordFactory::createOne();

        $result = $repo->getSingleAccountHash($record->id);

        self::assertNotNull($result);
        self::assertEquals($record->id, $result->id);
        self::assertEquals($record->syncHash, $result->syncHash);
        self::assertEqualsWithDelta($record->updatedAt, $result->updatedAt, 1);
    }

    public function testGetSingleHashMissing(): void
    {
        $repo = $this->getRepository();
        OtpRecordFactory::createOne();

        $result = $repo->getSingleAccountHash(4444);

        self::assertNull($result);
    }

    public function testFindExistingAccountHashExisting(): void
    {
        $repo = $this->getRepository();

        $record = OtpRecordFactory::createOne();
        OtpRecordFactory::createOne();

        $result = $repo->findExistingAccountHash($record->syncHash);

        self::assertNotNull($result);
        self::assertEquals($record->id, $result->id);
        self::assertEquals($record->syncHash, $result->syncHash);
        self::assertEqualsWithDelta($record->updatedAt, $result->updatedAt, 1);
    }

    public function testFindExistingAccountHashMissing(): void
    {
        $repo = $this->getRepository();
        OtpRecordFactory::createOne();

        $result = $repo->findExistingAccountHash('HelloWorld');

        self::assertNull($result);
    }

    public function testFindValid(): void
    {
        $repo = $this->getRepository();
        /** @var EncryptionService $encryptionService */
        $encryptionService = self::getContainer()->get(EncryptionService::class);
        $record = OtpRecordFactory::createOne();
        OtpRecordFactory::createOne();

        $result = $repo->find($record->id);

        self::assertNotNull($result);
        self::assertEquals($record->id, $result->id);
        self::assertEquals($encryptionService->decryptString($record->secret), $result->secret);
        self::assertEquals($record->syncHash, $result->syncHash);
        self::assertEquals($record->otpDigits, $result->otpDigits);
        self::assertEquals($record->totpStep, $result->totpStep);
        self::assertEquals($record->totpAlgorithm, $result->totpAlgorithm);
        self::assertEqualsWithDelta($record->createdAt, $result->createdAt, 1);
        self::assertEqualsWithDelta($record->updatedAt, $result->updatedAt, 1);
    }

    public function testFindMissing(): void
    {
        $repo = $this->getRepository();
        OtpRecordFactory::createOne();

        $result = $repo->find(1234);

        self::assertNull($result);
    }

    private function getRepository(): OtpRecordRepository
    {
        return self::getContainer()->get(OtpRecordRepository::class);
    }
}
