<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311214628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing_profile (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(64) DEFAULT NULL, country VARCHAR(64) DEFAULT NULL, city VARCHAR(64) DEFAULT NULL, address1 VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, vat_number VARCHAR(64) DEFAULT NULL, eu_vat_number VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE billing_profile_user (billing_profile_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8E0C1228409D7D29 (billing_profile_id), INDEX IDX_8E0C1228A76ED395 (user_id), PRIMARY KEY(billing_profile_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing_profile_user ADD CONSTRAINT FK_8E0C1228409D7D29 FOREIGN KEY (billing_profile_id) REFERENCES billing_profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_profile_user ADD CONSTRAINT FK_8E0C1228A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_account_user DROP FOREIGN KEY FK_B4571A16A76ED395');
        $this->addSql('ALTER TABLE billing_account_user DROP FOREIGN KEY FK_B4571A16170C302B');
        $this->addSql('DROP TABLE billing_account');
        $this->addSql('DROP TABLE billing_account_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing_account (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, country VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, city VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, address1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, address2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, vat_number VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, eu_vat_number VARCHAR(16) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE billing_account_user (billing_account_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4571A16170C302B (billing_account_id), INDEX IDX_B4571A16A76ED395 (user_id), PRIMARY KEY(billing_account_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE billing_account_user ADD CONSTRAINT FK_B4571A16A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_account_user ADD CONSTRAINT FK_B4571A16170C302B FOREIGN KEY (billing_account_id) REFERENCES billing_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_profile_user DROP FOREIGN KEY FK_8E0C1228409D7D29');
        $this->addSql('ALTER TABLE billing_profile_user DROP FOREIGN KEY FK_8E0C1228A76ED395');
        $this->addSql('DROP TABLE billing_profile');
        $this->addSql('DROP TABLE billing_profile_user');
    }
}
