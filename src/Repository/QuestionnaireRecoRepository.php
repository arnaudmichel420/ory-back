<?php

namespace App\Repository;

use App\Entity\QuestionnaireReco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

}
