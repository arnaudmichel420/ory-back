<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\QuestionnaireReco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionnaireReco>
 */
class QuestionnaireRecoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionnaireReco::class);
    }

    public function findActiveByLibelleWithQuestions(string $libelle): ?QuestionnaireReco
    {
        try {
            /** @var ?QuestionnaireReco $questionnaire */
            $questionnaire = $this->createQueryBuilder('questionnaire')
                ->addSelect('questions', 'choix')
                ->leftJoin('questionnaire.questionRecos', 'questions')
                ->leftJoin('questions.choixRecos', 'choix')
                ->andWhere('questionnaire.libelle = :libelle')
                ->andWhere('questionnaire.actif = true')
                ->setParameter('libelle', $libelle)
                ->orderBy('questions.ordre', 'ASC')
                ->addOrderBy('choix.id', 'ASC')
                ->getQuery()
                ->getOneOrNullResult();

            return $questionnaire;
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
