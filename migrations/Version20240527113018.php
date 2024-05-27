<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527113018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_instance (user_id INT NOT NULL, instance_id INT NOT NULL, INDEX IDX_A2BD55DEA76ED395 (user_id), INDEX IDX_A2BD55DE3A51721D (instance_id), PRIMARY KEY(user_id, instance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_instance ADD CONSTRAINT FK_A2BD55DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_instance ADD CONSTRAINT FK_A2BD55DE3A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_instance DROP FOREIGN KEY FK_A2BD55DEA76ED395');
        $this->addSql('ALTER TABLE user_instance DROP FOREIGN KEY FK_A2BD55DE3A51721D');
        $this->addSql('DROP TABLE user_instance');
    }
}
