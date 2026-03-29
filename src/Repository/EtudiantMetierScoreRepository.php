<?php

declare(strict_types=1);

namespace App\Repository;

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
}
