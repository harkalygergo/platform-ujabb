<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240509161748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE website ADD instance_id INT DEFAULT NULL, DROP instance');
        $this->addSql('ALTER TABLE website ADD CONSTRAINT FK_476F5DE73A51721D FOREIGN KEY (instance_id) REFERENCES instance (id)');
        $this->addSql('CREATE INDEX IDX_476F5DE73A51721D ON website (instance_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE website DROP FOREIGN KEY FK_476F5DE73A51721D');
        $this->addSql('DROP INDEX IDX_476F5DE73A51721D ON website');
        $this->addSql('ALTER TABLE website ADD instance INT NOT NULL, DROP instance_id');
    }
}
