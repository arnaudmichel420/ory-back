<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EtudiantReponseReco;
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
}
