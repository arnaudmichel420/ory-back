<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\EtudiantReponseReco;
use App\Entity\QuestionnaireReco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtudiantReponseReco>
 */
class EtudiantReponseRecoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtudiantReponseReco::class);
    }

    public function deleteByEtudiantAndQuestionnaire(Etudiant $etudiant, QuestionnaireReco $questionnaire): int
    {
        /** @var list<EtudiantReponseReco> $reponses */
        $reponses = $this->createQueryBuilder('reponse')
            ->innerJoin('reponse.choix', 'choix')
            ->innerJoin('choix.question', 'question')
            ->andWhere('reponse.etudiant = :etudiant')
            ->andWhere('question.questionnaire = :questionnaire')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('questionnaire', $questionnaire)
            ->getQuery()
            ->getResult();

        foreach ($reponses as $reponse) {
            $this->getEntityManager()->remove($reponse);
        }

        return \count($reponses);
    }
}
