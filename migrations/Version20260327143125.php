<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260327143125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant_metier_score CHANGE score_total score_total DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE metier_etudiant ADD CONSTRAINT FK_C4A38935D694F4B8 FOREIGN KEY (metier_code) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE metier_etudiant ADD CONSTRAINT FK_C4A38935DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE metier_attractivite ADD CONSTRAINT FK_23F62B21A260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE metier_attractivite ADD CONSTRAINT FK_23F62B21D0F97A8 FOREIGN KEY (territoire_id) REFERENCES territoire (id)');
        $this->addSql('ALTER TABLE metier_centre_interet ADD CONSTRAINT FK_35DBE8EA55BBC1E1 FOREIGN KEY (centre_interet_id) REFERENCES centre_interet (id)');
        $this->addSql('ALTER TABLE metier_centre_interet ADD CONSTRAINT FK_35DBE8EAA260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE metier_competence ADD CONSTRAINT FK_2B2C55BA260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE metier_competence ADD CONSTRAINT FK_2B2C55B50E3CB27 FOREIGN KEY (code_ogr_comp_id) REFERENCES competence (code_ogr)');
        $this->addSql('ALTER TABLE metier_contexte_travail ADD CONSTRAINT FK_B8734577A260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE metier_contexte_travail ADD CONSTRAINT FK_B87345771325556D FOREIGN KEY (code_ogr_contexte_id) REFERENCES contexte_travail (code_ogr)');
        $this->addSql('ALTER TABLE metier_secteur ADD CONSTRAINT FK_8D21C2A2A260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE metier_secteur ADD CONSTRAINT FK_8D21C2A29F7E4405 FOREIGN KEY (secteur_id) REFERENCES secteur (id)');
        $this->addSql('ALTER TABLE mobilite ADD CONSTRAINT FK_C2517C5335871C91 FOREIGN KEY (code_ogr_metier_source_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE question_quiz_defi ADD CONSTRAINT FK_1FF11B9E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz_defi (id)');
        $this->addSql('ALTER TABLE question_reco ADD CONSTRAINT FK_635D1782CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire_reco (id)');
        $this->addSql('ALTER TABLE quiz_defi ADD CONSTRAINT FK_16245F0573F00F27 FOREIGN KEY (defi_id) REFERENCES defi (id)');
        $this->addSql('ALTER TABLE secteur ADD CONSTRAINT FK_8045251F96B8568 FOREIGN KEY (sous_secteur_parent_id) REFERENCES secteur (id)');
        $this->addSql('ALTER TABLE sous_domaine ADD CONSTRAINT FK_5BA672814272FC9F FOREIGN KEY (domaine_id) REFERENCES domaine (id)');
        $this->addSql('ALTER TABLE territoire ADD CONSTRAINT FK_B8655F54B7B3B75E FOREIGN KEY (code_territoire_parent_id) REFERENCES territoire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant_metier_score CHANGE score_total score_total DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE metier_attractivite DROP FOREIGN KEY FK_23F62B21A260A932');
        $this->addSql('ALTER TABLE metier_attractivite DROP FOREIGN KEY FK_23F62B21D0F97A8');
        $this->addSql('ALTER TABLE metier_centre_interet DROP FOREIGN KEY FK_35DBE8EA55BBC1E1');
        $this->addSql('ALTER TABLE metier_centre_interet DROP FOREIGN KEY FK_35DBE8EAA260A932');
        $this->addSql('ALTER TABLE metier_competence DROP FOREIGN KEY FK_2B2C55BA260A932');
        $this->addSql('ALTER TABLE metier_competence DROP FOREIGN KEY FK_2B2C55B50E3CB27');
        $this->addSql('ALTER TABLE metier_contexte_travail DROP FOREIGN KEY FK_B8734577A260A932');
        $this->addSql('ALTER TABLE metier_contexte_travail DROP FOREIGN KEY FK_B87345771325556D');
        $this->addSql('ALTER TABLE metier_etudiant DROP FOREIGN KEY FK_C4A38935D694F4B8');
        $this->addSql('ALTER TABLE metier_etudiant DROP FOREIGN KEY FK_C4A38935DDEAB1A3');
        $this->addSql('ALTER TABLE metier_secteur DROP FOREIGN KEY FK_8D21C2A2A260A932');
        $this->addSql('ALTER TABLE metier_secteur DROP FOREIGN KEY FK_8D21C2A29F7E4405');
        $this->addSql('ALTER TABLE mobilite DROP FOREIGN KEY FK_C2517C5335871C91');
        $this->addSql('ALTER TABLE question_quiz_defi DROP FOREIGN KEY FK_1FF11B9E853CD175');
        $this->addSql('ALTER TABLE question_reco DROP FOREIGN KEY FK_635D1782CE07E8FF');
        $this->addSql('ALTER TABLE quiz_defi DROP FOREIGN KEY FK_16245F0573F00F27');
        $this->addSql('ALTER TABLE secteur DROP FOREIGN KEY FK_8045251F96B8568');
        $this->addSql('ALTER TABLE sous_domaine DROP FOREIGN KEY FK_5BA672814272FC9F');
        $this->addSql('ALTER TABLE territoire DROP FOREIGN KEY FK_B8655F54B7B3B75E');
    }
}
