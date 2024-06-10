<?php

namespace App\DoctrineType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Algorithm extends Type
{
    public const string NAME = 'algorithm';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return self::NAME;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
