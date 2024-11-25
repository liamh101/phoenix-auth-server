<?php

namespace App\Tests\Integration\Repository;

use App\Entity\User;
use App\Factory\OtpRecordFactory;
use App\Factory\UserFactory;
use App\Repository\OtpRecordRepository;
use App\Service\EncryptionService;
use App\Service\UserService;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Bundle\SecurityBundle\Security;

class OtpRecordRepositoryTest extends IntegrationTestCase
{

    public function testGetAllRecords(): void
    {
        $authUser = UserFactory::createOne();

        $repo = $this->getRepository($authUser->_real());
        /** @var EncryptionService $encryptionService */
        $encryptionService = self::getContainer()->get(EncryptionService::class);

        $third = OtpRecordFactory::new(['user' => $authUser])->create();
        $second = OtpRecordFactory::new(['user' => $authUser])->create();
        $first = OtpRecordFactory::new(['user' => $authUser])->create();

        OtpRecordFactory::new()->create();

        $result = $repo->getAll();

        self::assertCount(3, $result);
        self::assertEquals($first->id, $result[0]->id);
        self::assertEquals($first->name, $result[0]->name);
        self::assertEquals($encryptionService->decryptString($first->secret), $result[0]->secret);
        self::assertEquals($first->otpDigits, $result[0]->otpDigits);
        self::assertEquals($first->totpAlgorithm, $result[0]->totpAlgorithm);
        self::assertEquals($first->syncHash, $result[0]->syncHash);
        self::assertEqualsWithDelta($first->createdAt, $result[0]->createdAt, 1);
        self::assertEqualsWithDelta($first->updatedAt, $result[0]->updatedAt, 1);

        self::assertEquals($second->id, $result[1]->id);
        self::assertEquals($second->name, $result[1]->name);
        self::assertEquals($encryptionService->decryptString($second->secret), $result[1]->secret);
        self::assertEquals($second->otpDigits, $result[1]->otpDigits);
        self::assertEquals($second->totpAlgorithm, $result[1]->totpAlgorithm);
        self::assertEquals($second->syncHash, $result[1]->syncHash);
        self::assertEqualsWithDelta($second->createdAt, $result[1]->createdAt, 1);
        self::assertEqualsWithDelta($second->updatedAt, $result[1]->updatedAt, 1);

        self::assertEquals($third->id, $result[2]->id);
        self::assertEquals($third->name, $result[2]->name);
        self::assertEquals($encryptionService->decryptString($third->secret), $result[2]->secret);
        self::assertEquals($third->otpDigits, $result[2]->otpDigits);
        self::assertEquals($third->totpAlgorithm, $result[2]->totpAlgorithm);
        self::assertEquals($third->syncHash, $result[2]->syncHash);
        self::assertEqualsWithDelta($third->createdAt, $result[2]->createdAt, 1);
        self::assertEqualsWithDelta($third->updatedAt, $result[2]->updatedAt, 1);
    }

    public function testGetManifest(): void
    {
        $authUser = UserFactory::createOne();

        $repo = $this->getRepository($authUser->_real());

        $first = OtpRecordFactory::createOne(['user' => $authUser]);
        $second = OtpRecordFactory::createOne(['user' => $authUser]);
        $third = OtpRecordFactory::createOne(['user' => $authUser]);

        OtpRecordFactory::createOne();

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
        $authUser = UserFactory::createOne();

        $repo = $this->getRepository($authUser->_real());

        $record = OtpRecordFactory::createOne(['user' => $authUser]);
        OtpRecordFactory::createOne();

        $result = $repo->getSingleAccountHash($record->id);

        self::assertNotNull($result);
        self::assertEquals($record->id, $result->id);
        self::assertEquals($record->syncHash, $result->syncHash);
        self::assertEqualsWithDelta($record->updatedAt, $result->updatedAt, 1);
    }

    public function testGetSingleHashInvalidUser(): void
    {
        $authUser = UserFactory::createOne();

        $repo = $this->getRepository($authUser->_real());

        $record = OtpRecordFactory::createOne();
        OtpRecordFactory::createOne(['user' => $authUser]);

        $result = $repo->getSingleAccountHash($record->id);

        self::assertNull($result);
    }

    public function testGetSingleHashMissing(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());
        OtpRecordFactory::createOne(['user' => $authUser]);

        $result = $repo->getSingleAccountHash(4444);

        self::assertNull($result);
    }

    public function testFindExistingAccountHashExisting(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());

        $record = OtpRecordFactory::createOne(['user' => $authUser]);
        OtpRecordFactory::createOne(['user' => $authUser]);

        $result = $repo->findExistingAccountHash($record->syncHash);

        self::assertNotNull($result);
        self::assertEquals($record->id, $result->id);
        self::assertEquals($record->syncHash, $result->syncHash);
        self::assertEqualsWithDelta($record->updatedAt, $result->updatedAt, 1);
    }

    public function testFindExistingAccountHashInvalidUser(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());

        $record = OtpRecordFactory::createOne();
        $result = $repo->findExistingAccountHash($record->syncHash);

        self::assertNull($result);
    }


    public function testFindExistingAccountHashMissing(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());
        OtpRecordFactory::createOne(['user' => $authUser]);

        $result = $repo->findExistingAccountHash('HelloWorld');

        self::assertNull($result);
    }

    public function testFindValid(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());

        /** @var EncryptionService $encryptionService */
        $encryptionService = self::getContainer()->get(EncryptionService::class);
        $record = OtpRecordFactory::createOne(['user' => $authUser]);
        OtpRecordFactory::createOne(['user' => $authUser]);

        $result = $repo->getSingleRecord($record->id);

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

    public function testFindInvalidUser(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());
        $record = OtpRecordFactory::createOne();

        $result = $repo->getSingleRecord($record->id);

        self::assertNull($result);
    }

    public function testFindMissing(): void
    {
        $authUser = UserFactory::createOne();
        $repo = $this->getRepository($authUser->_real());
        OtpRecordFactory::createOne(['user' => $authUser]);

        $result = $repo->getSingleRecord(1234);

        self::assertNull($result);
    }

    private function getRepository(User $authenticatedUser): OtpRecordRepository
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')
            ->willReturn($authenticatedUser);

        self::getContainer()->set(Security::class, $security);

        return self::getContainer()->get(OtpRecordRepository::class);
    }
}
