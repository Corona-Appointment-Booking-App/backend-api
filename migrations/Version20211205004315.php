<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205004315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\' AFTER `uuid`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E00CEDDE6F949845 ON booking (time)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E00CEDDE6F949845 ON booking');
        $this->addSql('ALTER TABLE booking DROP time');
    }
}
