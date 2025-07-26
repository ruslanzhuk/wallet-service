<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719130307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE crypto_currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE network_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE crypto_currency (id INT NOT NULL, network_id_id INT DEFAULT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59320B70B15E270B ON crypto_currency (network_id_id)');
        $this->addSql('CREATE TABLE network (id INT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, explorer_url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE wallet (id INT NOT NULL, network_id_id INT DEFAULT NULL, public_address TEXT NOT NULL, private_key TEXT NOT NULL, mnemonic_path TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7C68921FB15E270B ON wallet (network_id_id)');
        $this->addSql('COMMENT ON COLUMN wallet.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE crypto_currency ADD CONSTRAINT FK_59320B70B15E270B FOREIGN KEY (network_id_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FB15E270B FOREIGN KEY (network_id_id) REFERENCES network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE crypto_currency_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE network_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE wallet_id_seq CASCADE');
        $this->addSql('ALTER TABLE crypto_currency DROP CONSTRAINT FK_59320B70B15E270B');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_7C68921FB15E270B');
        $this->addSql('DROP TABLE crypto_currency');
        $this->addSql('DROP TABLE network');
        $this->addSql('DROP TABLE wallet');
    }
}
