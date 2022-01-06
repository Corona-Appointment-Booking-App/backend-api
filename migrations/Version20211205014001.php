<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205014001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD test_center_id INT NOT NULL AFTER `uuid`');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEDBAF64BD FOREIGN KEY (test_center_id) REFERENCES test_center (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEDBAF64BD ON booking (test_center_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEDBAF64BD');
        $this->addSql('DROP INDEX IDX_E00CEDDEDBAF64BD ON booking');
        $this->addSql('ALTER TABLE booking DROP test_center_id');
    }
}
