<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241103213557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add direct relationship between user and record';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE otp_record ADD CONSTRAINT FK_3D1293AEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3D1293AEA76ED395 ON otp_record (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE otp_record DROP CONSTRAINT FK_3D1293AEA76ED395');
        $this->addSql('DROP INDEX IDX_3D1293AEA76ED395');
        $this->addSql('ALTER TABLE otp_record DROP user_id');
    }
}
