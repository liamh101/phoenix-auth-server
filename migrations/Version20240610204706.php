<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240610204706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create OTP record table to match client structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE otp_record_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE otp_record (id INT NOT NULL, name VARCHAR(255) NOT NULL, secret VARCHAR(255) NOT NULL, totp_step INT NOT NULL, otp_digits INT NOT NULL, totp_algorithm VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE otp_record_id_seq CASCADE');
        $this->addSql('DROP TABLE otp_record');
    }
}
