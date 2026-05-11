<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511163335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_defi (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, nombre_actions INT NOT NULL, supprime_le DATETIME DEFAULT NULL, defi_id INT NOT NULL, INDEX IDX_AA34185073F00F27 (defi_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE appellation (code_ogr VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, libelle_court VARCHAR(255) DEFAULT NULL, peu_utiliser TINYINT DEFAULT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, INDEX IDX_187A5B98A260A932 (code_ogr_metier_id), PRIMARY KEY (code_ogr)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE centre_interet (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, definition LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE choix_quiz_defi (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, est_correct TINYINT NOT NULL, supprime_le DATETIME DEFAULT NULL, question_quiz_id INT NOT NULL, INDEX IDX_5BBFC1488BE00468 (question_quiz_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE choix_reco (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, supprime_le DATETIME DEFAULT NULL, question_id INT NOT NULL, centre_interet_id INT DEFAULT NULL, secteur_id INT DEFAULT NULL, contexte_travail_id VARCHAR(255) DEFAULT NULL, INDEX IDX_68C1BD5F1E27F6BF (question_id), INDEX IDX_68C1BD5F55BBC1E1 (centre_interet_id), INDEX IDX_68C1BD5F9F7E4405 (secteur_id), INDEX IDX_68C1BD5FBEF18DE8 (contexte_travail_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE collectionnable (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, valeur VARCHAR(100) NOT NULL, supprime_le DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE collectionnable_defi (collectionnable_id INT NOT NULL, defi_id INT NOT NULL, INDEX IDX_CD3BE3CB21E09613 (collectionnable_id), INDEX IDX_CD3BE3CB73F00F27 (defi_id), PRIMARY KEY (collectionnable_id, defi_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE competence (code_ogr VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, transition_eco TINYINT DEFAULT NULL, transition_num TINYINT DEFAULT NULL, PRIMARY KEY (code_ogr)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE contexte_travail (code_ogr VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, type_contexte VARCHAR(255) DEFAULT NULL, PRIMARY KEY (code_ogr)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE defi (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, est_actif TINYINT NOT NULL, supprime_le DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, prerequis_id INT DEFAULT NULL, INDEX IDX_DCD5A35EED196790 (prerequis_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE domaine (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_78AF0ACC77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse LONGTEXT NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, telephone VARCHAR(20) NOT NULL, supprime_le DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_717E22E3FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiant_collectionnable (etudiant_id INT NOT NULL, collectionnable_id INT NOT NULL, INDEX IDX_9C51A336DDEAB1A3 (etudiant_id), INDEX IDX_9C51A33621E09613 (collectionnable_id), PRIMARY KEY (etudiant_id, collectionnable_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiant_defi (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(255) NOT NULL, progression INT DEFAULT NULL, complete_le DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, etudiant_id INT NOT NULL, defi_id INT NOT NULL, INDEX IDX_8CF40412DDEAB1A3 (etudiant_id), INDEX IDX_8CF4041273F00F27 (defi_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiant_metier_interaction (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, poids INT NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, etudiant_id INT NOT NULL, INDEX IDX_F5193B0EA260A932 (code_ogr_metier_id), INDEX IDX_F5193B0EDDEAB1A3 (etudiant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiant_metier_score (id INT AUTO_INCREMENT NOT NULL, score_total DOUBLE PRECISION DEFAULT 0 NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, etudiant_id INT NOT NULL, INDEX IDX_18BB547A260A932 (code_ogr_metier_id), INDEX IDX_18BB547DDEAB1A3 (etudiant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE etudiant_reponse_reco (id INT AUTO_INCREMENT NOT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, etudiant_id INT NOT NULL, choix_id INT NOT NULL, INDEX IDX_9059034DDEAB1A3 (etudiant_id), INDEX IDX_9059034D9144651 (choix_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier (code_ogr VARCHAR(255) NOT NULL, code_rome VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, definition LONGTEXT DEFAULT NULL, acces_metier LONGTEXT DEFAULT NULL, transition_eco TINYINT DEFAULT NULL, transition_num TINYINT DEFAULT NULL, emploi_reglemente TINYINT DEFAULT NULL, emploi_cadre TINYINT DEFAULT NULL, sous_domaine_id INT NOT NULL, INDEX IDX_51A00D8CA40AA975 (sous_domaine_id), PRIMARY KEY (code_ogr)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_etudiant (metier_code VARCHAR(255) NOT NULL, etudiant_id INT NOT NULL, INDEX IDX_C4A38935D694F4B8 (metier_code), INDEX IDX_C4A38935DDEAB1A3 (etudiant_id), PRIMARY KEY (metier_code, etudiant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_attractivite (id INT AUTO_INCREMENT NOT NULL, code_attractivite VARCHAR(32) NOT NULL, valeur INT NOT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, territoire_id INT NOT NULL, INDEX IDX_23F62B21A260A932 (code_ogr_metier_id), INDEX IDX_23F62B21D0F97A8 (territoire_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_attractivite_import_run (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(32) NOT NULL, total_pairs INT NOT NULL, processed_pairs INT NOT NULL, error_pairs INT NOT NULL, ignored_values INT NOT NULL, created_values INT NOT NULL, updated_values INT NOT NULL, deleted_values INT NOT NULL, total_batches INT NOT NULL, processed_batches INT NOT NULL, completed_at DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_centre_interet (id INT AUTO_INCREMENT NOT NULL, principal TINYINT DEFAULT NULL, centre_interet_id INT NOT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, INDEX IDX_35DBE8EA55BBC1E1 (centre_interet_id), INDEX IDX_35DBE8EAA260A932 (code_ogr_metier_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_competence (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, libelle_enjeu VARCHAR(255) DEFAULT NULL, coeur_metier INT DEFAULT 0 NOT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, code_ogr_comp_id VARCHAR(255) NOT NULL, INDEX IDX_2B2C55BA260A932 (code_ogr_metier_id), INDEX IDX_2B2C55B50E3CB27 (code_ogr_comp_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_contexte_travail (id INT AUTO_INCREMENT NOT NULL, libelle_groupe VARCHAR(255) DEFAULT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, code_ogr_contexte_id VARCHAR(255) NOT NULL, INDEX IDX_B8734577A260A932 (code_ogr_metier_id), INDEX IDX_B87345771325556D (code_ogr_contexte_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE metier_secteur (id INT AUTO_INCREMENT NOT NULL, principal TINYINT DEFAULT NULL, code_ogr_metier_id VARCHAR(255) NOT NULL, secteur_id INT NOT NULL, INDEX IDX_8D21C2A2A260A932 (code_ogr_metier_id), INDEX IDX_8D21C2A29F7E4405 (secteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE mobilite (id INT AUTO_INCREMENT NOT NULL, ordre_mobilite INT DEFAULT NULL, code_ogr_metier_cible VARCHAR(255) NOT NULL, code_ogr_metier_source_id VARCHAR(255) NOT NULL, INDEX IDX_C2517C5335871C91 (code_ogr_metier_source_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE question_quiz_defi (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, explication LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, ordre INT NOT NULL, supprime_le DATETIME DEFAULT NULL, quiz_id INT NOT NULL, INDEX IDX_1FF11B9E853CD175 (quiz_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE question_reco (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ordre INT NOT NULL, supprime_le DATETIME DEFAULT NULL, questionnaire_id INT NOT NULL, INDEX IDX_635D1782CE07E8FF (questionnaire_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE questionnaire_reco (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, actif TINYINT DEFAULT 1 NOT NULL, supprime_le DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE quiz_defi (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, supprime_le DATETIME DEFAULT NULL, defi_id INT NOT NULL, INDEX IDX_16245F0573F00F27 (defi_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE secteur (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, definition LONGTEXT DEFAULT NULL, sous_secteur_parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8045251F77153098 (code), INDEX IDX_8045251F96B8568 (sous_secteur_parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sous_domaine (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, domaine_id INT NOT NULL, UNIQUE INDEX UNIQ_5BA6728177153098 (code), INDEX IDX_5BA672814272FC9F (domaine_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE territoire (id INT AUTO_INCREMENT NOT NULL, code_type_territoire VARCHAR(255) DEFAULT NULL, code_territoire VARCHAR(255) DEFAULT NULL, libelle_territoire VARCHAR(255) DEFAULT NULL, code_type_territoire_parent VARCHAR(255) DEFAULT NULL, code_territoire_parent_id INT DEFAULT NULL, INDEX IDX_B8655F54B7B3B75E (code_territoire_parent_id), UNIQUE INDEX uniq_territoire_type_code (code_type_territoire, code_territoire), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, supprime_le DATETIME DEFAULT NULL, cree_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE action_defi ADD CONSTRAINT FK_AA34185073F00F27 FOREIGN KEY (defi_id) REFERENCES defi (id)');
        $this->addSql('ALTER TABLE appellation ADD CONSTRAINT FK_187A5B98A260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE choix_quiz_defi ADD CONSTRAINT FK_5BBFC1488BE00468 FOREIGN KEY (question_quiz_id) REFERENCES question_quiz_defi (id)');
        $this->addSql('ALTER TABLE choix_reco ADD CONSTRAINT FK_68C1BD5F1E27F6BF FOREIGN KEY (question_id) REFERENCES question_reco (id)');
        $this->addSql('ALTER TABLE choix_reco ADD CONSTRAINT FK_68C1BD5F55BBC1E1 FOREIGN KEY (centre_interet_id) REFERENCES centre_interet (id)');
        $this->addSql('ALTER TABLE choix_reco ADD CONSTRAINT FK_68C1BD5F9F7E4405 FOREIGN KEY (secteur_id) REFERENCES secteur (id)');
        $this->addSql('ALTER TABLE choix_reco ADD CONSTRAINT FK_68C1BD5FBEF18DE8 FOREIGN KEY (contexte_travail_id) REFERENCES contexte_travail (code_ogr)');
        $this->addSql('ALTER TABLE collectionnable_defi ADD CONSTRAINT FK_CD3BE3CB21E09613 FOREIGN KEY (collectionnable_id) REFERENCES collectionnable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collectionnable_defi ADD CONSTRAINT FK_CD3BE3CB73F00F27 FOREIGN KEY (defi_id) REFERENCES defi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE defi ADD CONSTRAINT FK_DCD5A35EED196790 FOREIGN KEY (prerequis_id) REFERENCES defi (id)');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE etudiant_collectionnable ADD CONSTRAINT FK_9C51A336DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant_collectionnable ADD CONSTRAINT FK_9C51A33621E09613 FOREIGN KEY (collectionnable_id) REFERENCES collectionnable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant_defi ADD CONSTRAINT FK_8CF40412DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE etudiant_defi ADD CONSTRAINT FK_8CF4041273F00F27 FOREIGN KEY (defi_id) REFERENCES defi (id)');
        $this->addSql('ALTER TABLE etudiant_metier_interaction ADD CONSTRAINT FK_F5193B0EA260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE etudiant_metier_interaction ADD CONSTRAINT FK_F5193B0EDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE etudiant_metier_score ADD CONSTRAINT FK_18BB547A260A932 FOREIGN KEY (code_ogr_metier_id) REFERENCES metier (code_ogr)');
        $this->addSql('ALTER TABLE etudiant_metier_score ADD CONSTRAINT FK_18BB547DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE etudiant_reponse_reco ADD CONSTRAINT FK_9059034DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE etudiant_reponse_reco ADD CONSTRAINT FK_9059034D9144651 FOREIGN KEY (choix_id) REFERENCES choix_reco (id)');
        $this->addSql('ALTER TABLE metier ADD CONSTRAINT FK_51A00D8CA40AA975 FOREIGN KEY (sous_domaine_id) REFERENCES sous_domaine (id)');
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
        $this->addSql('ALTER TABLE action_defi DROP FOREIGN KEY FK_AA34185073F00F27');
        $this->addSql('ALTER TABLE appellation DROP FOREIGN KEY FK_187A5B98A260A932');
        $this->addSql('ALTER TABLE choix_quiz_defi DROP FOREIGN KEY FK_5BBFC1488BE00468');
        $this->addSql('ALTER TABLE choix_reco DROP FOREIGN KEY FK_68C1BD5F1E27F6BF');
        $this->addSql('ALTER TABLE choix_reco DROP FOREIGN KEY FK_68C1BD5F55BBC1E1');
        $this->addSql('ALTER TABLE choix_reco DROP FOREIGN KEY FK_68C1BD5F9F7E4405');
        $this->addSql('ALTER TABLE choix_reco DROP FOREIGN KEY FK_68C1BD5FBEF18DE8');
        $this->addSql('ALTER TABLE collectionnable_defi DROP FOREIGN KEY FK_CD3BE3CB21E09613');
        $this->addSql('ALTER TABLE collectionnable_defi DROP FOREIGN KEY FK_CD3BE3CB73F00F27');
        $this->addSql('ALTER TABLE defi DROP FOREIGN KEY FK_DCD5A35EED196790');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3FB88E14F');
        $this->addSql('ALTER TABLE etudiant_collectionnable DROP FOREIGN KEY FK_9C51A336DDEAB1A3');
        $this->addSql('ALTER TABLE etudiant_collectionnable DROP FOREIGN KEY FK_9C51A33621E09613');
        $this->addSql('ALTER TABLE etudiant_defi DROP FOREIGN KEY FK_8CF40412DDEAB1A3');
        $this->addSql('ALTER TABLE etudiant_defi DROP FOREIGN KEY FK_8CF4041273F00F27');
        $this->addSql('ALTER TABLE etudiant_metier_interaction DROP FOREIGN KEY FK_F5193B0EA260A932');
        $this->addSql('ALTER TABLE etudiant_metier_interaction DROP FOREIGN KEY FK_F5193B0EDDEAB1A3');
        $this->addSql('ALTER TABLE etudiant_metier_score DROP FOREIGN KEY FK_18BB547A260A932');
        $this->addSql('ALTER TABLE etudiant_metier_score DROP FOREIGN KEY FK_18BB547DDEAB1A3');
        $this->addSql('ALTER TABLE etudiant_reponse_reco DROP FOREIGN KEY FK_9059034DDEAB1A3');
        $this->addSql('ALTER TABLE etudiant_reponse_reco DROP FOREIGN KEY FK_9059034D9144651');
        $this->addSql('ALTER TABLE metier DROP FOREIGN KEY FK_51A00D8CA40AA975');
        $this->addSql('ALTER TABLE metier_etudiant DROP FOREIGN KEY FK_C4A38935D694F4B8');
        $this->addSql('ALTER TABLE metier_etudiant DROP FOREIGN KEY FK_C4A38935DDEAB1A3');
        $this->addSql('ALTER TABLE metier_attractivite DROP FOREIGN KEY FK_23F62B21A260A932');
        $this->addSql('ALTER TABLE metier_attractivite DROP FOREIGN KEY FK_23F62B21D0F97A8');
        $this->addSql('ALTER TABLE metier_centre_interet DROP FOREIGN KEY FK_35DBE8EA55BBC1E1');
        $this->addSql('ALTER TABLE metier_centre_interet DROP FOREIGN KEY FK_35DBE8EAA260A932');
        $this->addSql('ALTER TABLE metier_competence DROP FOREIGN KEY FK_2B2C55BA260A932');
        $this->addSql('ALTER TABLE metier_competence DROP FOREIGN KEY FK_2B2C55B50E3CB27');
        $this->addSql('ALTER TABLE metier_contexte_travail DROP FOREIGN KEY FK_B8734577A260A932');
        $this->addSql('ALTER TABLE metier_contexte_travail DROP FOREIGN KEY FK_B87345771325556D');
        $this->addSql('ALTER TABLE metier_secteur DROP FOREIGN KEY FK_8D21C2A2A260A932');
        $this->addSql('ALTER TABLE metier_secteur DROP FOREIGN KEY FK_8D21C2A29F7E4405');
        $this->addSql('ALTER TABLE mobilite DROP FOREIGN KEY FK_C2517C5335871C91');
        $this->addSql('ALTER TABLE question_quiz_defi DROP FOREIGN KEY FK_1FF11B9E853CD175');
        $this->addSql('ALTER TABLE question_reco DROP FOREIGN KEY FK_635D1782CE07E8FF');
        $this->addSql('ALTER TABLE quiz_defi DROP FOREIGN KEY FK_16245F0573F00F27');
        $this->addSql('ALTER TABLE secteur DROP FOREIGN KEY FK_8045251F96B8568');
        $this->addSql('ALTER TABLE sous_domaine DROP FOREIGN KEY FK_5BA672814272FC9F');
        $this->addSql('ALTER TABLE territoire DROP FOREIGN KEY FK_B8655F54B7B3B75E');
        $this->addSql('DROP TABLE action_defi');
        $this->addSql('DROP TABLE appellation');
        $this->addSql('DROP TABLE centre_interet');
        $this->addSql('DROP TABLE choix_quiz_defi');
        $this->addSql('DROP TABLE choix_reco');
        $this->addSql('DROP TABLE collectionnable');
        $this->addSql('DROP TABLE collectionnable_defi');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE contexte_travail');
        $this->addSql('DROP TABLE defi');
        $this->addSql('DROP TABLE domaine');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('DROP TABLE etudiant_collectionnable');
        $this->addSql('DROP TABLE etudiant_defi');
        $this->addSql('DROP TABLE etudiant_metier_interaction');
        $this->addSql('DROP TABLE etudiant_metier_score');
        $this->addSql('DROP TABLE etudiant_reponse_reco');
        $this->addSql('DROP TABLE metier');
        $this->addSql('DROP TABLE metier_etudiant');
        $this->addSql('DROP TABLE metier_attractivite');
        $this->addSql('DROP TABLE metier_attractivite_import_run');
        $this->addSql('DROP TABLE metier_centre_interet');
        $this->addSql('DROP TABLE metier_competence');
        $this->addSql('DROP TABLE metier_contexte_travail');
        $this->addSql('DROP TABLE metier_secteur');
        $this->addSql('DROP TABLE mobilite');
        $this->addSql('DROP TABLE question_quiz_defi');
        $this->addSql('DROP TABLE question_reco');
        $this->addSql('DROP TABLE questionnaire_reco');
        $this->addSql('DROP TABLE quiz_defi');
        $this->addSql('DROP TABLE secteur');
        $this->addSql('DROP TABLE sous_domaine');
        $this->addSql('DROP TABLE territoire');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
