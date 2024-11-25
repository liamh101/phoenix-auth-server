<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240721094756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Created At and Update At columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE otp_record ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN otp_record.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record DROP created_at');
        $this->addSql('ALTER TABLE otp_record DROP updated_at');
    }
}
