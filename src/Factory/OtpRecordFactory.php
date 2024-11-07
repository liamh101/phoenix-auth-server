<?php

namespace App\Factory;

use App\Entity\OtpRecord;
use Zenstruck\Foundry\LazyValue;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<OtpRecord>
 */
final class OtpRecordFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return OtpRecord::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->name(),
            'secret' => self::faker()->text(100),
            'syncHash' => self::faker()->text(128),
            'otpDigits' => self::faker()->randomNumber(),
            'totpStep' => self::faker()->randomNumber(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'updatedAt' => self::faker()->dateTime(),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(OtpRecord $otpRecord): void {})
        ;
    }
}
