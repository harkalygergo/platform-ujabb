<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311213329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing_account (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(64) DEFAULT NULL, country VARCHAR(64) DEFAULT NULL, city VARCHAR(64) DEFAULT NULL, address1 VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, vat_number VARCHAR(64) DEFAULT NULL, eu_vat_number VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE billing_account_user (billing_account_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4571A16170C302B (billing_account_id), INDEX IDX_B4571A16A76ED395 (user_id), PRIMARY KEY(billing_account_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing_account_user ADD CONSTRAINT FK_B4571A16170C302B FOREIGN KEY (billing_account_id) REFERENCES billing_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_account_user ADD CONSTRAINT FK_B4571A16A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing_account_user DROP FOREIGN KEY FK_B4571A16170C302B');
        $this->addSql('ALTER TABLE billing_account_user DROP FOREIGN KEY FK_B4571A16A76ED395');
        $this->addSql('DROP TABLE billing_account');
        $this->addSql('DROP TABLE billing_account_user');
    }
}
