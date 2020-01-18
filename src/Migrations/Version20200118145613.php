<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200118145613 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__message AS SELECT id, texte, date FROM message');
        $this->addSql('DROP TABLE message');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, texte CLOB DEFAULT NULL COLLATE BINARY, date DATETIME NOT NULL, auteur_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO message (id, texte, date) SELECT id, texte, date FROM __temp__message');
        $this->addSql('DROP TABLE __temp__message');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__message AS SELECT id, texte, date FROM message');
        $this->addSql('DROP TABLE message');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, texte CLOB DEFAULT NULL, date DATETIME NOT NULL, auteur CLOB DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO message (id, texte, date) SELECT id, texte, date FROM __temp__message');
        $this->addSql('DROP TABLE __temp__message');
    }
}
