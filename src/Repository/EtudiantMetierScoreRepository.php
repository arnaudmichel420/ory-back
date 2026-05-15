<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\EtudiantMetierScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtudiantMetierScore>
 */
class EtudiantMetierScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtudiantMetierScore::class);
    }

    public function getLastRecommendationDateForEtudiant(Etudiant $etudiant): ?\DateTimeImmutable
    {
        $result = $this->createQueryBuilder('ems')
            ->select('ems.creeLe AS creeLe')
            ->where('ems.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('ems.creeLe', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['creeLe'] ?? null;
    }
}
