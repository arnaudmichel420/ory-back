<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContexteTravail;
use App\Entity\Etudiant;
use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContexteTravail>
 */
class ContexteTravailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContexteTravail::class);
    }

    public function getContextesTravailFromMetier(Metier $metier): array
    {
        return $this->createQueryBuilder('ct')
            ->select('DISTINCT ct.codeOgr')
            ->join('ct.metierContexteTravails', 'mci', 'WITH', 'mci.codeOgrMetier = :metier')
            ->setParameter('metier', $metier)
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getContextesTravailFromOnboarding(Etudiant $etudiant): array
    {
        return $this->createQueryBuilder('ct')
            ->select('DISTINCT ct.codeOgr')
            ->join('ct.choixRecos', 'cr')
            ->join('cr.etudiantReponseRecos', 'err', 'WITH', 'err.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->getQuery()
            ->getSingleColumnResult();
    }
}
