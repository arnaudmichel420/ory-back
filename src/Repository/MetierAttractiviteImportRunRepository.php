<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MetierAttractiviteImportRun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MetierAttractiviteImportRun>
 */
class MetierAttractiviteImportRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetierAttractiviteImportRun::class);
    }
}
