<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260406210731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant_metier_score CHANGE score_total score_total DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_territoire_type_code ON territoire (code_type_territoire, code_territoire)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant_metier_score CHANGE score_total score_total DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('DROP INDEX uniq_territoire_type_code ON territoire');
    }
}
