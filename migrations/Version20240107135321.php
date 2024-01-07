<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240107135321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE website (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(128) NOT NULL, title VARCHAR(255) DEFAULT NULL, slogan VARCHAR(255) DEFAULT NULL, keywords VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, robots VARCHAR(32) NOT NULL, language VARCHAR(8) DEFAULT NULL, template VARCHAR(64) DEFAULT NULL, logo INT DEFAULT NULL, favicon INT DEFAULT NULL, primary_color VARCHAR(16) DEFAULT NULL, secondary_color VARCHAR(16) DEFAULT NULL, html_head LONGTEXT DEFAULT NULL, html_body LONGTEXT DEFAULT NULL, html_footer LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE website');
    }
}
