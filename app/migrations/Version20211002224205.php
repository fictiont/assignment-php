<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002224205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO language (ISO, name, rtl) VALUES ("eng","English",0),("ger","German",0),("lav","Latvian",0),("ukr","Ukrainian",0),("ara","Arabic",1)');
       
        $this->addSql('INSERT INTO translation_key (id, key_code, description) VALUES ("941bf97c-c896-4f38-86d0-8d64dafcbd56", "main.title", "Title of the main page"), ("fd7eb701-5d03-4399-bec3-f4672e91d393", "main.body.greetings", "Greetings in the main page body")');
        
        $this->addSql('INSERT INTO translation (id, language_ISO, key_id, translation) VALUES ("b6cfe355-cbb0-40c4-b315-f342feb6c21f", "eng", "941bf97c-c896-4f38-86d0-8d64dafcbd56", "Hello world"),("c2294529-3e48-4951-8c2c-27fb4b2875e5", "ger", "941bf97c-c896-4f38-86d0-8d64dafcbd56", "Hallo Welt"),("2e4588e3-f7e9-4a36-83f1-cda745e6b790", "lav", "941bf97c-c896-4f38-86d0-8d64dafcbd56", "Sveika pasaule"),("f65e3dab-bebb-45ce-958b-13f1cc0ad27d", "ukr", "941bf97c-c896-4f38-86d0-8d64dafcbd56", "Привіт світ")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM translation');
        $this->addSql('DELETE FROM translation_key');
        $this->addSql('DELETE FROM language');
    }
}
