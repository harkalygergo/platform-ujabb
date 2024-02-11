<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211143906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD default_instance_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EEA261AB FOREIGN KEY (default_instance_id) REFERENCES instance (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649EEA261AB ON user (default_instance_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EEA261AB');
        $this->addSql('DROP INDEX IDX_8D93D649EEA261AB ON user');
        $this->addSql('ALTER TABLE user DROP default_instance_id');
    }
}
