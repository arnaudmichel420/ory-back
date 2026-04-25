<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Metier\MetierSavedListQueryDto;
use App\Entity\Etudiant;
use App\Entity\EtudiantMetierInteraction;
use App\Entity\Metier;
use App\Enum\EtudiantMetierInteractionTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findOneByEtudiantMetierAndType(
        Etudiant $etudiant,
        Metier $metier,
        EtudiantMetierInteractionTypeEnum $type,
    ): ?EtudiantMetierInteraction {
        return $this->createQueryBuilder('interaction')
            ->andWhere('interaction.etudiant = :etudiant')
            ->andWhere('interaction.codeOgrMetier = :metier')
            ->andWhere('interaction.type = :type')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('metier', $metier)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array{items: list<Metier>, total: int}
     */
    public function paginateSavedMetiers(Etudiant $etudiant, MetierSavedListQueryDto $query): array
    {
        $paginator = new Paginator(
            $this->createQueryBuilder('interaction')
                ->select('interaction, metier, ms_list, s_list')
                ->innerJoin('interaction.codeOgrMetier', 'metier')
                ->leftJoin('metier.metierSecteurs', 'ms_list')
                ->leftJoin('ms_list.secteur', 's_list')
                ->andWhere('interaction.etudiant = :etudiant')
                ->andWhere('interaction.type = :type')
                ->setParameter('etudiant', $etudiant)
                ->setParameter('type', EtudiantMetierInteractionTypeEnum::SAUVEGARDE)
                ->orderBy('interaction.creeLe', 'DESC')
                ->addOrderBy('metier.codeOgr', 'ASC')
                ->setFirstResult($query->getOffset())
                ->setMaxResults($query->limit)
                ->getQuery(),
            true,
        );

        /** @var list<EtudiantMetierInteraction> $interactions */
        $interactions = iterator_to_array($paginator->getIterator(), false);

        return [
            'items' => array_map(
                static fn (EtudiantMetierInteraction $interaction): Metier => $interaction->getCodeOgrMetier(),
                $interactions,
            ),
            'total' => \count($paginator),
        ];
    }
}
