<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260412200559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metier_attractivite_import_run (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(32) NOT NULL, total_pairs INT NOT NULL, processed_pairs INT NOT NULL, error_pairs INT NOT NULL, ignored_values INT NOT NULL, created_values INT NOT NULL, updated_values INT NOT NULL, deleted_values INT NOT NULL, total_batches INT NOT NULL, processed_batches INT NOT NULL, completed_at DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE etudiant_metier_score CHANGE score_total score_total DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE metier_attractivite CHANGE code_attractivite code_attractivite VARCHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE metier_attractivite_import_run');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE etudiant_metier_score CHANGE score_total score_total DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE metier_attractivite CHANGE code_attractivite code_attractivite VARCHAR(255) NOT NULL');
    }
}
