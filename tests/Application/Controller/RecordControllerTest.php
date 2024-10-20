<?php

namespace App\Tests\Application\Controller;

use App\Factory\OtpRecordFactory;
use App\Service\EncryptionService;
use App\Tests\Application\ApplicationTestCase;

class RecordControllerTest  extends ApplicationTestCase
{
    public function testGetManifest(): void
    {
        $third = OtpRecordFactory::createOne();
        sleep(1);
        $second = OtpRecordFactory::createOne();
        sleep(1);
        $first = OtpRecordFactory::createOne();

        $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/records/manifest', server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(1, $data['version']);
        self::assertCount(3, $data['data']);

        self::assertEquals($first->id, $data['data'][0]['id']);
        self::assertEquals($first->updatedAt->format('U'), $data['data'][0]['updatedAt']);

        self::assertEquals($second->id, $data['data'][1]['id']);
        self::assertEquals($second->updatedAt->format('U'), $data['data'][1]['updatedAt']);

        self::assertEquals($third->id, $data['data'][2]['id']);
        self::assertEquals($third->updatedAt->format('U'), $data['data'][2]['updatedAt']);
    }

    public function testGetManifestZeroResults(): void
    {
        $this->createAuthenticatedClient();
        $this->client->request('GET', '/api/records/manifest', server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(1, $data['version']);
        self::assertCount(0, $data['data']);
    }

    public function testGetManifestOneThousandResults(): void
    {
        OtpRecordFactory::createMany(1000);

        $this->createAuthenticatedClient();
        $this->client->request('GET', '/api/records/manifest', server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(1, $data['version']);
        self::assertCount(1000, $data['data']);
    }

    public function testGetSingleRecord(): void
    {
        /** @var EncryptionService $encryptionService */
        $encryptionService = self::getContainer()->get(EncryptionService::class);

        $record = OtpRecordFactory::createOne();
        $recordSecret = $encryptionService->decryptString($record->secret);

        $this->createAuthenticatedClient();
        $this->client->request('GET', '/api/records/' . $record->id, server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(1, $data['version']);
        self::assertEquals($record->id, $data['data']['id']);
        self::assertEquals($record->name, $data['data']['name']);
        self::assertEquals($recordSecret, $data['data']['secret']);
        self::assertEquals($record->totpStep, $data['data']['totpStep']);
        self::assertEquals($record->otpDigits, $data['data']['otpDigits']);
        self::assertEquals($record->totpAlgorithm, $data['data']['algorithm']);
        self::assertEquals($record->syncHash, $data['data']['syncHash']);
        self::assertEquals($record->updatedAt->format('U'), $data['data']['updatedAt']);
    }

    public function testGetSingleRecordWithAlgorithm(): void
    {
        /** @var EncryptionService $encryptionService */
        $encryptionService = self::getContainer()->get(EncryptionService::class);

        $record = OtpRecordFactory::createOne(['totpAlgorithm' => 'sha1']);
        $recordSecret = $encryptionService->decryptString($record->secret);

        $this->createAuthenticatedClient();
        $this->client->request('GET', '/api/records/' . $record->id, server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(1, $data['version']);
        self::assertEquals($record->id, $data['data']['id']);
        self::assertEquals($record->name, $data['data']['name']);
        self::assertEquals($recordSecret, $data['data']['secret']);
        self::assertEquals($record->totpStep, $data['data']['totpStep']);
        self::assertEquals($record->otpDigits, $data['data']['otpDigits']);
        self::assertEquals($record->totpAlgorithm, $data['data']['algorithm']);
        self::assertEquals($record->syncHash, $data['data']['syncHash']);
        self::assertEquals($record->updatedAt->format('U'), $data['data']['updatedAt']);
    }

    public function testGetSingleRecordNotFound(): void
    {
        $this->createAuthenticatedClient();
        $this->client->request('GET', '/api/records/9102', server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Record could not be found', $data['message']);
    }

    /**
     * @dataProvider validOptionsProvider
     */
    public function testCreateRecord(string $key, string|int|null $value): void
    {
        $data = [
            'name' => 'Hello World 1',
            'secret' => 'thisIsATest',
            'totpStep' => 30,
            'otpDigits' => 8,
            'totpAlgorithm' => null
        ];

        $data[$key] = $value;

        $this->createAuthenticatedClient();
        $this->client->request(
            'POST',
            '/api/records',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data, JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $updatedAt = new \DateTime();

        self::assertEquals(1, $data['version']);
        self::assertTrue(isset($data['data']['id']));
        self::assertTrue(isset($data['data']['syncHash']));
        self::assertTrue(isset($data['data']['updatedAt']));

        self::assertEqualsWithDelta($updatedAt->format('U'), $data['data']['updatedAt'], 1);
    }

    /**
     * @dataProvider validOptionsProvider
     */
    public function testUpdateRecord(string $key, string|int|null $value): void
    {
        $record = OtpRecordFactory::createOne();

        $data = [
            'id' => $record->id,
            'name' => 'Hello World 1',
            'secret' => 'thisIsATest',
            'totpStep' => 30,
            'otpDigits' => 8,
            'totpAlgorithm' => null
        ];

        $data[$key] = $value;

        $this->createAuthenticatedClient();
        $this->client->request(
            'PUT',
            '/api/records/' . $record->id,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data, JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $updatedAt = new \DateTime();

        self::assertEquals(1, $data['version']);
        self::assertEquals($record->id, $data['data']['id']);
        self::assertNotEquals($record->syncHash, $data['data']['syncHash']);
        self::assertTrue(isset($data['data']['updatedAt']));

        self::assertEqualsWithDelta($updatedAt->format('U'), $data['data']['updatedAt'], 1);
    }

    private function validOptionsProvider(): array
    {
        return [
            ['totpStep', 30],
            ['totpStep', 60],
            ['totpStep', 90],
            ['totpStep', 120],
            ['otpDigits', 6],
            ['otpDigits', 8],
            ['totpAlgorithm', null],
            ['totpAlgorithm', 'sha1'],
            ['totpAlgorithm', 'sha256'],
            ['totpAlgorithm', 'sha512'],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testCreateRecordInvalid(string $key, string|int|null $value): void
    {
        $data = [
            'name' => 'Hello World 1',
            'secret' => 'thisIsATest',
            'totpStep' => 30,
            'otpDigits' => 8,
            'totpAlgorithm' => null
        ];

        $data[$key] = $value;

        $this->createAuthenticatedClient();
        $this->client->request(
            'POST',
            '/api/records',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data, JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testUpdateRecordInvalid(string $key, string|int|null $value): void
    {
        $record = OtpRecordFactory::createOne();

        $data = [
            'name' => 'Hello World 1',
            'secret' => 'thisIsATest',
            'totpStep' => 30,
            'otpDigits' => 8,
            'totpAlgorithm' => null
        ];

        $data[$key] = $value;

        $this->createAuthenticatedClient();
        $this->client->request(
            'PUT',
            '/api/records/' . $record->id,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data, JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(422);
    }

    private function invalidProvider(): array
    {
        return [
            ['name', null],
            ['secret', null],
            ['totpStep',  null],
            ['totpStep', 400],
            ['otpDigits', null],
            ['otpDigits', 78],
            ['totpAlgorithm', 'sha12345'],
            ['totpAlgorithm', 123],
        ];
    }

    public function testDeleteRecord(): void
    {
        $record = OtpRecordFactory::createOne();

        $this->createAuthenticatedClient();
        $this->client->request(
            'DELETE',
            '/api/records/' . $record->id,
            server: ['CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseIsSuccessful();

        $this->client->request(
            'GET',
            '/api/records/' . $record->id,
            server: ['CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @dataProvider urlProvider
     */
    public function testNoAuthentication(string $method, string $url): void
    {
        OtpRecordFactory::createMany(3);

        $this->client->request($method, $url, server: ['CONTENT_TYPE' => 'application/json']);
        self::assertResponseStatusCodeSame(401);
    }

    //Update records
    // Delete records
    private function urlProvider(): array
    {
        return [
          ['GET', '/api/records/manifest'],
          ['GET', '/api/records/1'],
          ['PUT', '/api/records/1'],
          ['DELETE', '/api/records/1'],
          ['POST', '/api/records'],
        ];
    }
}