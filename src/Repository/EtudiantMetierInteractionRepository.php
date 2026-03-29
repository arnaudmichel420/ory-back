<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EtudiantMetierInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtudiantMetierInteraction>
 */
class EtudiantMetierInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtudiantMetierInteraction::class);
    }
}
