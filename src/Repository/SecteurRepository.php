<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\Metier;
use App\Entity\Secteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Secteur>
 */
class SecteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secteur::class);
    }

    /**
     * @return list<int>
     */
    public function getSecteursFromMetier(Metier $metier): array
    {
        $secteurs = $this->createQueryBuilder('s')
            ->select('DISTINCT s.id')
            ->join('s.metierSecteurs', 'ms', 'WITH', 'ms.codeOgrMetier = :metier')
            ->setParameter('metier', $metier)
            ->getQuery()
            ->getSingleColumnResult();

        return array_map('intval', $secteurs);
    }

    /**
     * @return list<int>
     */
    public function getSecteursFromOnboarding(Etudiant $etudiant): array
    {
        $secteurs = $this->createQueryBuilder('s')
            ->select('DISTINCT s.id')
            ->join('s.choixRecos', 'cr')
            ->join('cr.etudiantReponseRecos', 'err', 'WITH', 'err.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->getQuery()
            ->getSingleColumnResult();

        return array_map('intval', $secteurs);
    }
}
