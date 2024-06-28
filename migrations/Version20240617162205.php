<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617162205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON user_instance');
        $this->addSql('ALTER TABLE user_instance ADD PRIMARY KEY (instance_id, user_id)');
        $this->addSql('ALTER TABLE website_page CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON user_instance');
        $this->addSql('ALTER TABLE user_instance ADD PRIMARY KEY (user_id, instance_id)');
        $this->addSql('ALTER TABLE website_page CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
