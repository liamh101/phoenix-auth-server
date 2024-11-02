<?php

namespace App\Tests\Integration\Repository;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Tests\Integration\IntegrationTestCase;

class UserRepositoryTest extends IntegrationTestCase
{

    public function testFindSingleUser(): void
    {
        $repo = $this->getRepository();

        UserFactory::createOne();
        $userTwo = UserFactory::createOne();
        UserFactory::createOne();

        $result = $repo->findExistingAccount($userTwo->getEmail());

        self::assertEquals($userTwo->getId(), $result->getId());
        self::assertEquals($userTwo->getEmail(), $result->getEmail());
        self::assertEquals($userTwo->getPassword(), $result->getPassword());
    }

    public function testDeleteOtherUsers(): void
    {
        $repo = $this->getRepository();

        $userOne = UserFactory::createOne();
        $userTwo = UserFactory::createOne(['email' => 'test@test.com']);
        $userThree = UserFactory::createOne(['email' => 'test2@test.com']);

        $repo->deleteOtherUsers($userTwo->_real());

        $expectedMissingOne = $repo->find($userOne->getId());
        $expectedStillExists = $repo->find($userTwo->getId());
        $expectedMissingTwo = $repo->find($userThree->getId());

        self::assertNull($expectedMissingOne);
        self::assertNull($expectedMissingTwo);

        self::assertEquals($userTwo->getId(), $expectedStillExists->getId());
    }

    private function getRepository(): UserRepository
    {
        return self::getContainer()->get(UserRepository::class);
    }
}
