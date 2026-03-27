CREATE TABLE `quiz_defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `defi_id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text
);

CREATE TABLE `question_quiz_defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `quiz_id` int NOT NULL,
  `question` varchar(255) NOT NULL,
  `explication` text,
  `type` ENUM ('vrai_faux', 'multiple', 'simple') NOT NULL,
  `ordre` int NOT NULL
);

CREATE TABLE `choix_quiz_defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `question_quiz_id` int NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `est_correct` boolean NOT NULL
);

CREATE TABLE `defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `type` ENUM ('action', 'quiz') NOT NULL,
  `est_actif` boolean NOT NULL,
  `prerequis` int
);

CREATE TABLE `action_defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `defi_id` int NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text,
  `nombre_actions` int NOT NULL
);

CREATE TABLE `collectionnable` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `valeur` varchar(100) NOT NULL
);

CREATE TABLE `defi_collectionnable` (
  `defi_id` int NOT NULL,
  `collectionnable_id` int NOT NULL
);

CREATE TABLE `etudiant` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `utilisateur_id` int,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `ville` varchar(255) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `telephone` varchar(20) NOT NULL
);

CREATE TABLE `utilisateur` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `roles` json
);

CREATE TABLE `etudiant_defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `defi_id` int NOT NULL,
  `statut` ENUM ('en_cours', 'termine', 'abandonne') NOT NULL,
  `progression` int,
  `complete_le` date
);

CREATE TABLE `etudiant_collectionnable` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `collectionnable_id` int NOT NULL
);

CREATE TABLE `metier_favori` (
  `metier_id` varchar(255) NOT NULL,
  `etudiant_id` int NOT NULL
);

CREATE TABLE `metier` (
  `code_ogr` varchar(255) PRIMARY KEY COMMENT 'Identifiant OGR unique',
  `code_rome` varchar(255) NOT NULL COMMENT 'Code ROME (ex: M1805)',
  `libelle` varchar(255) NOT NULL COMMENT 'Intitulé du métier',
  `definition` text COMMENT 'Description du métier',
  `acces_metier` text COMMENT 'Voies d''''accès au métier',
  `transition_eco` boolean COMMENT 'Marqueur transition écologique',
  `transition_num` boolean COMMENT 'Marqueur transition numérique',
  `emploi_reglemente` boolean COMMENT 'Emploi soumis à réglementation',
  `emploi_cadre` boolean COMMENT 'Emploi de cadre',
  `sous_domaine_id` int COMMENT 'Catégorie de navigation'
);

CREATE TABLE `appellation` (
  `code_ogr` varchar(255) PRIMARY KEY,
  `libelle` varchar(255) NOT NULL,
  `libelle_court` varchar(255),
  `code_ogr_metier` varchar(255) NOT NULL,
  `peu_usite` boolean COMMENT 'Appellation peu utilisée, à dé-prioriser dans la recherche'
);

CREATE TABLE `domaine` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code` varchar(255) UNIQUE NOT NULL,
  `libelle` varchar(255) NOT NULL
);

CREATE TABLE `sous_domaine` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code` varchar(255) UNIQUE NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `domaine_id` int NOT NULL
);

CREATE TABLE `competence` (
  `code_ogr` varchar(255) PRIMARY KEY,
  `libelle` varchar(255) NOT NULL,
  `type` ENUM ('savoir_faire', 'savoir_etre_professionel', 'savoir') NOT NULL,
  `transition_eco` boolean,
  `transition_num` boolean
);

CREATE TABLE `metier_competence` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `code_ogr_comp` varchar(255) NOT NULL,
  `type` ENUM ('savoir_faire', 'savoir_etre_professionel', 'savoir') NOT NULL,
  `libelle_enjeu` varchar(255) COMMENT 'Regroupement affiché dans la fiche métier',
  `coeur_metier` int NOT NULL DEFAULT 0 COMMENT 'Poids pour le score de reco : 0 = secondaire | 1 = émergente | 2 = principale'
);

CREATE TABLE `centre_interet` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `definition` text
);

CREATE TABLE `metier_centre_interet` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `centre_interet_id` int NOT NULL,
  `code_ogr_metier` varchar(255) NOT NULL,
  `principal` boolean COMMENT 'true = lien fort → à pondérer davantage dans le score de reco'
);

CREATE TABLE `secteur` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code` varchar(255) UNIQUE NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `definition` text,
  `secteur_parent_id` int COMMENT 'NULL = secteur racine, non NULL = sous-secteur'
);

CREATE TABLE `metier_secteur` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `secteur_id` int NOT NULL,
  `principal` boolean COMMENT 'Secteur principal du métier'
);

CREATE TABLE `contexte_travail` (
  `code_ogr` varchar(255) PRIMARY KEY,
  `libelle` varchar(255) NOT NULL,
  `type_contexte` varchar(255) COMMENT 'Catégorie : horaires, déplacements, environnement, etc.'
);

CREATE TABLE `metier_contexte_travail` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `code_ogr_contexte` varchar(255) NOT NULL,
  `libelle_groupe` varchar(255) COMMENT 'Groupe affiché dans la fiche métier'
);

CREATE TABLE `mobilite` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier_source` varchar(255) NOT NULL,
  `code_ogr_metier_cible` varchar(255) NOT NULL COMMENT 'Entier dans le JSON source (ex: 28, 403950). Référence metier.code_ogr casté en int.',
  `ordre_mobilite` int COMMENT 'Ordre de priorité : 1 = transition la plus naturelle'
);

CREATE TABLE `etudiant_metier_interaction` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `etudiant_id` int NOT NULL,
  `type` ENUM ('vue', 'sauvegarde', 'challenge') NOT NULL,
  `poids` int NOT NULL COMMENT 'vue=1 | challenge=3 | sauvegarde=4'
);

CREATE TABLE `etudiant_metier_score` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `etudiant_id` int NOT NULL,
  `score_total` float NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `metier_attractivite` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `code_attractivite` varchar(255) NOT NULL,
  `territoire_id` int NOT NULL,
  `valeur` int NOT NULL
);

CREATE TABLE `territoire` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_type_territoire` ENUM ('DEP', 'REG', 'NAT'),
  `code_territoire` varchar(255),
  `libelle_territoire` varchar(255),
  `code_type_territoire_parent` ENUM ('DEP', 'REG', 'NAT'),
  `code_territoire_parent` int
);

CREATE TABLE `questionnaire_reco` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `actif` boolean NOT NULL DEFAULT true
);

CREATE TABLE `question_reco` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `questionnaire_id` int NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `type` ENUM ('single', 'multi') NOT NULL,
  `ordre` int NOT NULL
);

CREATE TABLE `choix_reco` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `centre_interet_id` int,
  `secteur_id` int,
  `contexte_travail_id` varchar(255)
);

CREATE TABLE `etudiant_reponse_reco` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `choix_id` int NOT NULL,
  `repondu_le` datetime NOT NULL
);

CREATE INDEX `idx_reco_utilisateur` ON `etudiant_metier_score` (`etudiant_id`, `score_total`);

ALTER TABLE `defi` ADD FOREIGN KEY (`prerequis`) REFERENCES `defi` (`id`);

ALTER TABLE `etudiant` ADD FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

ALTER TABLE `quiz_defi` ADD FOREIGN KEY (`defi_id`) REFERENCES `defi` (`id`);

ALTER TABLE `question_quiz_defi` ADD FOREIGN KEY (`quiz_id`) REFERENCES `quiz_defi` (`id`);

ALTER TABLE `choix_quiz_defi` ADD FOREIGN KEY (`question_quiz_id`) REFERENCES `question_quiz_defi` (`id`);

ALTER TABLE `action_defi` ADD FOREIGN KEY (`defi_id`) REFERENCES `defi` (`id`);

ALTER TABLE `defi_collectionnable` ADD FOREIGN KEY (`defi_id`) REFERENCES `defi` (`id`);

ALTER TABLE `defi_collectionnable` ADD FOREIGN KEY (`collectionnable_id`) REFERENCES `collectionnable` (`id`);

ALTER TABLE `etudiant_defi` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `etudiant_defi` ADD FOREIGN KEY (`defi_id`) REFERENCES `defi` (`id`);

ALTER TABLE `etudiant_collectionnable` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `etudiant_collectionnable` ADD FOREIGN KEY (`collectionnable_id`) REFERENCES `collectionnable` (`id`);

ALTER TABLE `metier_favori` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `metier_favori` ADD FOREIGN KEY (`metier_id`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `metier` ADD FOREIGN KEY (`sous_domaine_id`) REFERENCES `sous_domaine` (`id`);

ALTER TABLE `appellation` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `sous_domaine` ADD FOREIGN KEY (`domaine_id`) REFERENCES `domaine` (`id`);

ALTER TABLE `metier_competence` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `metier_competence` ADD FOREIGN KEY (`code_ogr_comp`) REFERENCES `competence` (`code_ogr`);

ALTER TABLE `metier_centre_interet` ADD FOREIGN KEY (`centre_interet_id`) REFERENCES `centre_interet` (`id`);

ALTER TABLE `metier_centre_interet` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `secteur` ADD FOREIGN KEY (`secteur_parent_id`) REFERENCES `secteur` (`id`);

ALTER TABLE `metier_secteur` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `metier_secteur` ADD FOREIGN KEY (`secteur_id`) REFERENCES `secteur` (`id`);

ALTER TABLE `metier_contexte_travail` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `metier_contexte_travail` ADD FOREIGN KEY (`code_ogr_contexte`) REFERENCES `contexte_travail` (`code_ogr`);

ALTER TABLE `mobilite` ADD FOREIGN KEY (`code_ogr_metier_source`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `etudiant_metier_interaction` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `etudiant_metier_interaction` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `etudiant_metier_score` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `etudiant_metier_score` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `metier_attractivite` ADD FOREIGN KEY (`territoire_id`) REFERENCES `territoire` (`id`);

ALTER TABLE `metier_attractivite` ADD FOREIGN KEY (`code_ogr_metier`) REFERENCES `metier` (`code_ogr`);

ALTER TABLE `territoire` ADD FOREIGN KEY (`code_territoire_parent`) REFERENCES `territoire` (`id`);

ALTER TABLE `question_reco` ADD FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaire_reco` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`question_id`) REFERENCES `question_reco` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`centre_interet_id`) REFERENCES `centre_interet` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`secteur_id`) REFERENCES `secteur` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`contexte_travail_id`) REFERENCES `contexte_travail` (`code_ogr`);

ALTER TABLE `etudiant_reponse_reco` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `etudiant_reponse_reco` ADD FOREIGN KEY (`choix_id`) REFERENCES `choix_reco` (`id`);
