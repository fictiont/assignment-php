<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002220627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE language (iso VARCHAR(3) NOT NULL, name VARCHAR(70) NOT NULL, rtl TINYINT(1) NOT NULL, INDEX language_name (name), PRIMARY KEY(iso)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', key_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', language_ISO VARCHAR(3) DEFAULT NULL, translation VARCHAR(512) NOT NULL, INDEX IDX_B469456F267992E7 (language_ISO), INDEX IDX_B469456FD145533 (key_id), UNIQUE INDEX uniq_translation_key_language (language_ISO, key_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation_key (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', key_code VARCHAR(120) NOT NULL, description VARCHAR(512) NOT NULL, UNIQUE INDEX UNIQ_AADCBD56F112EB51 (key_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F267992E7 FOREIGN KEY (language_ISO) REFERENCES language (iso)');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456FD145533 FOREIGN KEY (key_id) REFERENCES translation_key (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F267992E7');
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456FD145533');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE translation');
        $this->addSql('DROP TABLE translation_key');
    }
}
