<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240613221008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Generated hash on opt record';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record ADD COLUMN sync_hash VARCHAR(128) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('alter table otp_record drop column sync_hash');
    }
}
