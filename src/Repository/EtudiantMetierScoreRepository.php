<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Metier\MetierSavedListQueryDto;
use App\Entity\Etudiant;
use App\Entity\EtudiantMetierScore;
use App\Entity\Metier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function getLastRecommendationDateForEtudiant(Etudiant $etudiant): ?\DateTimeImmutable
    {
        $result = $this->createQueryBuilder('ems')
            ->select('ems.creeLe AS creeLe')
            ->where('ems.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('ems.creeLe', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['creeLe'] ?? null;
    }

    /**
     * @return array{items: list<Metier>, total: int}
     */
    public function paginateRecommendedMetiers(Etudiant $etudiant, MetierSavedListQueryDto $query): array
    {
        $paginator = new Paginator(
            $this->createQueryBuilder('score')
                ->select('score, metier, ms_list, s_list, saved_etudiant')
                ->innerJoin('score.codeOgrMetier', 'metier')
                ->leftJoin('metier.metierSecteurs', 'ms_list')
                ->leftJoin('ms_list.secteur', 's_list')
                ->leftJoin('metier.etudiants', 'saved_etudiant', 'WITH', 'saved_etudiant = :etudiant')
                ->andWhere('score.etudiant = :etudiant')
                ->setParameter('etudiant', $etudiant)
                ->orderBy('score.scoreTotal', 'DESC')
                ->addOrderBy('metier.codeOgr', 'ASC')
                ->setFirstResult($query->getOffset())
                ->setMaxResults($query->limit)
                ->getQuery(),
            true,
        );

        /** @var list<EtudiantMetierScore> $scores */
        $scores = iterator_to_array($paginator->getIterator(), false);

        $items = [];
        foreach ($scores as $score) {
            $metier = $score->getCodeOgrMetier();

            if (!$metier instanceof Metier) {
                continue;
            }

            $metier->setSaved(!$metier->getEtudiants()->isEmpty());
            $items[] = $metier;
        }

        return [
            'items' => $items,
            'total' => \count($paginator),
        ];
    }
}
