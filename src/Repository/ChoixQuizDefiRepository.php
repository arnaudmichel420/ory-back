<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ChoixQuizDefi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChoixQuizDefi>
 */
class ChoixQuizDefiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoixQuizDefi::class);
    }
}
