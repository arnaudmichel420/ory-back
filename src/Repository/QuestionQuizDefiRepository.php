<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\QuestionQuizDefi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionQuizDefi>
 */
class QuestionQuizDefiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionQuizDefi::class);
    }
}
