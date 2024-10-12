<?php

namespace App\Tests\Service;

use App\Service\EncryptionService;
use PHPUnit\Framework\TestCase;

class EncryptionServiceTest extends TestCase
{

    /**
     * @dataProvider encryptionTextProvider
     */
    public function testEncryptString($originalText): void
    {
        $service = $this->makeService();

        $encryptionResult = $service->encryptString($originalText);
        $decryptedText = $service->decryptString($encryptionResult);

        self::assertNotEquals($originalText, $encryptionResult);
        self::assertEquals($originalText, $decryptedText);
    }

    private function encryptionTextProvider(): array
    {
        return [
            ['This is a test'],
            ['JBSWY3DPEHPK3PXP'],
        ];
    }

    private function makeService(): EncryptionService
    {
        return new EncryptionService('HelloW0rld');
    }
}
