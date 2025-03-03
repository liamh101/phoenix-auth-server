<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250301221052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add colour to an OTP record';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record ADD colour VARCHAR(6) DEFAULT \'5c636a\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record DROP colour');
    }
}
