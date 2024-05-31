<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531141251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE instance_file (id INT AUTO_INCREMENT NOT NULL, original_name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, instance_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_A49566DB3A51721D (instance_id), INDEX IDX_A49566DBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE instance_file ADD CONSTRAINT FK_A49566DB3A51721D FOREIGN KEY (instance_id) REFERENCES instance (id)');
        $this->addSql('ALTER TABLE instance_file ADD CONSTRAINT FK_A49566DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instance_file DROP FOREIGN KEY FK_A49566DB3A51721D');
        $this->addSql('ALTER TABLE instance_file DROP FOREIGN KEY FK_A49566DBA76ED395');
        $this->addSql('DROP TABLE instance_file');
    }
}
