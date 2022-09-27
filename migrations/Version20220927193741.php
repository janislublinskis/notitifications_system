<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927193741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE agent_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE api_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agent (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_268B9C9DE7927C74 ON agent (email)');
        $this->addSql('CREATE TABLE api_token (id INT NOT NULL, agent_id INT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7BA2F5EB3414710B ON api_token (agent_id)');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(32) NOT NULL, email VARCHAR(255) NOT NULL, phone_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB3414710B FOREIGN KEY (agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE agent_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE api_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE api_token DROP CONSTRAINT FK_7BA2F5EB3414710B');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE client');
    }
}
