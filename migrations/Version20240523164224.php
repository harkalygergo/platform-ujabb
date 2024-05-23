<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240523164224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE module_printbox_saved_project (id INT AUTO_INCREMENT NOT NULL, site VARCHAR(255) DEFAULT NULL, customer BIGINT DEFAULT NULL, project_hash VARCHAR(255) DEFAULT NULL, project_title VARCHAR(255) DEFAULT NULL, product BIGINT UNSIGNED DEFAULT NULL, variant BIGINT UNSIGNED DEFAULT NULL, product_title VARCHAR(255) DEFAULT NULL, product_category VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE module_printbox_saved_project');
    }
}
