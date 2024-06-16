<?php

namespace App\Service;

readonly class EncryptionService
{
    public const string CIPHER = 'aes-128-cbc';
    private const string DIGEST_ALGO = 'SHA256';

    public function __construct(
        private string $encryptionKey,
    ) {
    }

    public function encryptString(string $originalString): string
    {
        $ivlen = $this->getIvLength();
        $key = $this->generateKey();
        $iv = openssl_random_pseudo_bytes($ivlen);

        if (!$iv) {
            throw new \RuntimeException('Could not generate IV!');
        }

        $encryptedText = openssl_encrypt($originalString, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encryptedText);
    }

    public function decryptString(string $encryptedString): string
    {
        $ivlen = $this->getIvLength();
        $key = $this->generateKey();

        $details = base64_decode($encryptedString);

        $iv = substr($details, 0, $ivlen);
        $encryptedText = substr($details, $ivlen);

        return openssl_decrypt($encryptedText, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
    }

    private function getIvLength(): int
    {
        $ivLen = openssl_cipher_iv_length(self::CIPHER);

        if (!$ivLen) {
            throw new \RuntimeException('Could not generate IV length!');
        }

        return $ivLen;
    }

    private function generateKey(): string
    {
        $key = openssl_digest($this->encryptionKey, self::DIGEST_ALGO, TRUE);

        if (!$key) {
            throw new \RuntimeException('Could not generate encryption key');
        }

        return $key;
    }
}
