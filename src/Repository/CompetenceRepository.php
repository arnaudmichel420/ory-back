<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Competence;
use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Competence>
 */
class CompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competence::class);
    }

    /**
     * @return list<string>
     */
    public function getCompetencesFromMetier(Metier $metier): array
    {
        $competences = $this->createQueryBuilder('c')
            ->select('DISTINCT c.codeOgr')
            ->join('c.metierCompetences', 'mc', 'WITH', 'mc.codeOgrMetier = :metier')
            ->setParameter('metier', $metier)
            ->getQuery()
            ->getSingleColumnResult();

        return array_map('strval', $competences);
    }
}
