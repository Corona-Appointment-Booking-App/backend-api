<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211111134508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX opening_time_search_index_uuid ON opening_time (uuid)');
        $this->addSql('CREATE INDEX opening_time_search_index_time ON opening_time (time)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX opening_time_search_index_uuid ON opening_time');
        $this->addSql('DROP INDEX opening_time_search_index_time ON opening_time');
    }
}
