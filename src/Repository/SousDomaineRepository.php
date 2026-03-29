<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SousDomaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousDomaine>
 */
class SousDomaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousDomaine::class);
    }
}
