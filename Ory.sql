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
  `type` varchar(50) NOT NULL,
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
  `type` varchar(50) NOT NULL,
  `categorie` varchar(50) NOT NULL,
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
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `telephone` varchar(100) NOT NULL
);

CREATE TABLE `utilisateur` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(100) NOT NULL,
  `roles` json
);

CREATE TABLE `etudiant_defi` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `defi_id` int NOT NULL,
  `statut` varchar(50) NOT NULL,
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
  `acces_metier` text COMMENT 'Voies d''accès au métier',
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
  `type` varchar(255) NOT NULL COMMENT 'savoir_faire | savoir_etre_professionel | savoir',
  `transition_eco` boolean,
  `transition_num` boolean
);

CREATE TABLE `metier_competence` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `code_ogr_comp` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'savoir_faire | savoir_etre_professionel | savoir',
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
  `code_ogr_metier_cible` int NOT NULL COMMENT 'Entier dans le JSON source (ex: 28, 403950). Référence metier.code_ogr casté en int.',
  `ordre_mobilite` int COMMENT 'Ordre de priorité : 1 = transition la plus naturelle'
);

CREATE TABLE `etudiant_metier_interaction` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code_ogr_metier` varchar(255) NOT NULL,
  `etudiant_id` int NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'vue | sauvegarde | challenge ',
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
  `code_territoire` int,
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
  `type` varchar(50) NOT NULL COMMENT 'single | multi',
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

ALTER TABLE `metier` COMMENT = 'Fusion de referentiel_code_rome + fiche_emploi_metier. Point central du schéma.';

ALTER TABLE `appellation` COMMENT = 'Intitulés alternatifs d''un métier. Utiles pour l''autocomplétion et la recherche full-text.';

ALTER TABLE `domaine` COMMENT = 'Grand domaine professionnel (ex: Communication, médias). 14 domaines dans le ROME.';

ALTER TABLE `sous_domaine` COMMENT = 'Sous-domaine professionnel. Permet la navigation par catégorie dans l''app.';

ALTER TABLE `competence` COMMENT = 'Fusion de referentiel_competence et referentiel_savoir. Le champ ''type'' distingue les trois catégories.';

ALTER TABLE `metier_competence` COMMENT = 'Fusion de fiche_competence + fiche_savoir. Lien entre un métier et ses compétences requises. coeur_metier utilisable directement comme poids dans SUM() pour le scoring.';

ALTER TABLE `centre_interet` COMMENT = 'Centres d''intérêt ROME. Point d''entrée principal du questionnaire de recommandation.';

ALTER TABLE `metier_centre_interet` COMMENT = 'Lien entre centres d''intérêt et métiers. Colonne ''principal'' à utiliser comme poids dans l''algo de reco.';

ALTER TABLE `secteur` COMMENT = 'Fusion de arbo_secteur + arbo_sous_secteur via auto-référence. Deux niveaux max dans le ROME.';

ALTER TABLE `metier_secteur` COMMENT = 'Secteurs d''activité où s''exerce le métier. Filtre utile dans le questionnaire (ex: ''tu veux travailler dans quel secteur ?'').';

ALTER TABLE `contexte_travail` COMMENT = 'Conditions de travail. Exploitable dans le questionnaire pour matcher les préférences utilisateur.';

ALTER TABLE `metier_contexte_travail` COMMENT = 'Contextes de travail d''un métier.';

ALTER TABLE `mobilite` COMMENT = 'Passerelles entre métiers. Utilisable pour suggérer des métiers proches ou des reconversions.';

ALTER TABLE `etudiant_metier_score` COMMENT = 'Cache du score collaboratif par user/métier. Recalcul via job Symfony Messenger en arrière-plan.';

ALTER TABLE `etudiant_reponse_reco` COMMENT = 'Réponses brutes de l''étudiant au questionnaire initial';

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

ALTER TABLE `metier` ADD FOREIGN KEY (`code_ogr`) REFERENCES `metier_favori` (`metier_id`);

ALTER TABLE `etudiant` ADD FOREIGN KEY (`id`) REFERENCES `etudiant_metier_interaction` (`etudiant_id`);

ALTER TABLE `etudiant` ADD FOREIGN KEY (`id`) REFERENCES `etudiant_metier_score` (`etudiant_id`);

ALTER TABLE `metier` ADD FOREIGN KEY (`code_ogr`) REFERENCES `etudiant_metier_score` (`code_ogr_metier`);

ALTER TABLE `metier` ADD FOREIGN KEY (`code_ogr`) REFERENCES `etudiant_metier_interaction` (`code_ogr_metier`);

ALTER TABLE `territoire` ADD FOREIGN KEY (`code_territoire_parent`) REFERENCES `territoire` (`id`);

ALTER TABLE `territoire` ADD FOREIGN KEY (`id`) REFERENCES `metier_attractivite` (`territoire_id`);

ALTER TABLE `metier` ADD FOREIGN KEY (`code_ogr`) REFERENCES `metier_attractivite` (`code_ogr_metier`);

ALTER TABLE `question_reco` ADD FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaire_reco` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`question_id`) REFERENCES `question_reco` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`centre_interet_id`) REFERENCES `centre_interet` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`secteur_id`) REFERENCES `secteur` (`id`);

ALTER TABLE `choix_reco` ADD FOREIGN KEY (`contexte_travail_id`) REFERENCES `contexte_travail` (`code_ogr`);

ALTER TABLE `etudiant_reponse_reco` ADD FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

ALTER TABLE `etudiant_reponse_reco` ADD FOREIGN KEY (`choix_id`) REFERENCES `choix_reco` (`id`);
