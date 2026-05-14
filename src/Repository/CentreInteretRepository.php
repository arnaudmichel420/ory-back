<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CentreInteret;
use App\Entity\Etudiant;
use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CentreInteret>
 */
class CentreInteretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentreInteret::class);
    }

    public function getCentreInteretFromMetier(Metier $metier): array
    {
        return $this->createQueryBuilder('ci')
            ->select('DISTINCT ci.id')
            ->join('ci.metierCentreInterets', 'mci', 'WITH', 'mci.codeOgrMetier = :metier')
            ->setParameter('metier', $metier)
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getCentreInteretFromOnboarding(Etudiant $etudiant): array
    {
        return $this->createQueryBuilder('ci')
            ->select('DISTINCT ci.id')
            ->join('ci.choixRecos', 'cr')
            ->join('cr.etudiantReponseRecos', 'err', 'WITH', 'err.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->getQuery()
            ->getSingleColumnResult();
    }
}
