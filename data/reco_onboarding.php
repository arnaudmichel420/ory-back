<?php

declare(strict_types=1);

use App\Enum\QuestionRecoTypeEnum;

return [
    'libelle' => 'Onboarding recommandation métiers',
    'questions' => [
        [
            'ordre' => 1,
            'libelle' => 'Quelles activités te donnent envie de t\'investir ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Créer, construire ou rénover', 'centreInteretId' => 1],
                ['libelle' => 'Prendre soin des autres', 'centreInteretId' => 22],
                ['libelle' => 'Faire des découvertes', 'centreInteretId' => 10],
                ['libelle' => 'Organiser et planifier', 'centreInteretId' => 18],
            ],
        ],
        [
            'ordre' => 2,
            'libelle' => 'Quels domaines t\'attirent le plus aujourd\'hui ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Santé', 'secteurCode' => '85'],
                ['libelle' => 'Informatique et télécommunication', 'secteurCode' => '96'],
                ['libelle' => 'Environnement', 'secteurCode' => '80'],
                ['libelle' => 'Bâtiment et travaux publics', 'secteurCode' => '99'],
                ['libelle' => 'Enseignement et formation', 'secteurCode' => '112'],
                ['libelle' => 'Social', 'secteurCode' => '109'],
                ['libelle' => 'Sport, animation et loisir', 'secteurCode' => '93'],
                ['libelle' => 'Communication et marketing', 'secteurCode' => '94'],
                ['libelle' => 'Culture et patrimoine', 'secteurCode' => '82'],
                ['libelle' => 'Énergie', 'secteurCode' => '108'],
            ],
        ],
        [
            'ordre' => 3,
            'libelle' => 'Dans quel contexte te vois-tu travailler ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Avec une possibilité de télétravail', 'contexteTravailCodeOgr' => '300799'],
                ['libelle' => 'En extérieur', 'contexteTravailCodeOgr' => '300783'],
                ['libelle' => 'Avec des déplacements professionnels', 'contexteTravailCodeOgr' => '300779'],
                ['libelle' => 'En laboratoire', 'contexteTravailCodeOgr' => '23380'],
            ],
        ],
        [
            'ordre' => 4,
            'libelle' => 'Quelle relation aux autres te correspond le mieux ?',
            'type' => QuestionRecoTypeEnum::SINGLE,
            'choix' => [
                ['libelle' => 'Être au contact des gens', 'centreInteretId' => 4],
                ['libelle' => 'Communiquer et convaincre', 'centreInteretId' => 8],
                ['libelle' => 'Servir l\'intérêt public', 'centreInteretId' => 5],
                ['libelle' => 'M\'occuper d\'enfants', 'centreInteretId' => 21],
            ],
        ],
        [
            'ordre' => 5,
            'libelle' => 'Qu\'aimes-tu manipuler ou mobiliser ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Des chiffres et des données', 'centreInteretId' => 15],
                ['libelle' => 'Des outils numériques', 'centreInteretId' => 29],
                ['libelle' => 'Ton énergie physique', 'centreInteretId' => 13],
                ['libelle' => 'Tes mains et ton savoir-faire', 'centreInteretId' => 14],
            ],
        ],
        [
            'ordre' => 6,
            'libelle' => 'Quels secteurs concrets pourraient te plaire ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Industries', 'secteurCode' => '84'],
                ['libelle' => 'Logistique et transport', 'secteurCode' => '113'],
                ['libelle' => 'Hôtellerie et restauration', 'secteurCode' => '102'],
                ['libelle' => 'Commerce et distribution', 'secteurCode' => '88'],
                ['libelle' => 'Agriculture et élevage', 'secteurCode' => '79'],
                ['libelle' => 'Automobile', 'secteurCode' => '95'],
                ['libelle' => 'Finance, banque et assurance', 'secteurCode' => '91'],
                ['libelle' => 'Gestion administrative et ressources humaines', 'secteurCode' => '103'],
                ['libelle' => 'Service public, défense et sécurité', 'secteurCode' => '111'],
                ['libelle' => 'Tourisme', 'secteurCode' => '101'],
            ],
        ],
        [
            'ordre' => 7,
            'libelle' => 'Quelles contraintes pourrais-tu accepter ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Travailler les week-ends et jours fériés', 'contexteTravailCodeOgr' => '23376'],
                ['libelle' => 'Travailler de nuit', 'contexteTravailCodeOgr' => '300803'],
                ['libelle' => 'Porter ou manipuler des charges lourdes', 'contexteTravailCodeOgr' => '300795'],
                ['libelle' => 'Rester debout longtemps', 'contexteTravailCodeOgr' => '300800'],
            ],
        ],
        [
            'ordre' => 8,
            'libelle' => 'Quel impact aimerais-tu avoir ?',
            'type' => QuestionRecoTypeEnum::SINGLE,
            'choix' => [
                ['libelle' => 'Protéger l\'environnement', 'centreInteretId' => 20],
                ['libelle' => 'Protéger et secourir', 'centreInteretId' => 23],
                ['libelle' => 'Transmettre des savoirs', 'centreInteretId' => 24],
                ['libelle' => 'Faire respecter les règles', 'centreInteretId' => 11],
            ],
        ],
        [
            'ordre' => 9,
            'libelle' => 'Dans quel type de lieu te projettes-tu ?',
            'type' => QuestionRecoTypeEnum::MULTI,
            'choix' => [
                ['libelle' => 'Dans un établissement de santé', 'contexteTravailCodeOgr' => '38126'],
                ['libelle' => 'Au domicile d\'un particulier', 'contexteTravailCodeOgr' => '300778'],
                ['libelle' => 'Sur une zone régionale', 'contexteTravailCodeOgr' => '23381'],
                ['libelle' => 'Sur une ligne ou un îlot de production', 'contexteTravailCodeOgr' => '23382'],
            ],
        ],
        [
            'ordre' => 10,
            'libelle' => 'Quelle forme d\'expression te ressemble le plus ?',
            'type' => QuestionRecoTypeEnum::SINGLE,
            'choix' => [
                ['libelle' => 'Créer comme un artiste', 'centreInteretId' => 30],
                ['libelle' => 'Jouer avec les mots', 'centreInteretId' => 12],
                ['libelle' => 'Dessiner ou représenter visuellement', 'centreInteretId' => 7],
                ['libelle' => 'Cuisiner et transformer des produits', 'centreInteretId' => 27],
            ],
        ],
    ],
];
