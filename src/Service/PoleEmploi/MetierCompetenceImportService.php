<?php

declare(strict_types=1);

namespace App\Service\PoleEmploi;

use App\Entity\MetierCompetence;
use App\Enum\MetierCompetenceTypeEnum;
use App\Repository\MetierCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;

class MetierCompetenceImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetierCompetenceRepository $metierCompetenceRepository,
        private readonly PoleEmploiImportUtils $utils,
    ) {
    }

    /**
     * @param array<string, mixed> $sources
     * @param array<string, int>   $resume
     */
    public function importer(array $sources, ImportContext $contexte, array &$resume): void
    {
        $existantesParMetier = [];
        foreach ($this->metierCompetenceRepository->findAll() as $liaison) {
            $codeMetier = $liaison->getCodeOgrMetier()?->getCodeOgr();
            $codeCompetence = $liaison->getCodeOgrComp()?->getCodeOgr();
            $type = $liaison->getType()?->value;

            if (null !== $codeMetier && null !== $codeCompetence && null !== $type) {
                $existantesParMetier[$codeMetier][$codeCompetence.'|'.$type] = $liaison;
            }
        }

        foreach ($contexte->metiersParCodeRome as $codeRome => $metier) {
            $codeMetier = $metier->getCodeOgr();
            $fiche = $contexte->fichesParRome[$codeRome] ?? null;

            if (null === $codeMetier || null === $fiche) {
                continue;
            }

            $desirees = [];
            $this->ajouterCompetencesSouhaitees($desirees, $fiche['competences']['savoir_faire']['enjeux'] ?? [], MetierCompetenceTypeEnum::SAVOIR_FAIRE);
            $this->ajouterCompetencesSouhaitees($desirees, $fiche['competences']['savoir_etre_professionnel']['enjeux'] ?? [], MetierCompetenceTypeEnum::SAVOIR_ETRE_PROFESSIONEL);
            $this->ajouterCompetencesSouhaitees($desirees, $fiche['competences']['savoirs']['categories'] ?? [], MetierCompetenceTypeEnum::SAVOIR);

            $existantes = $existantesParMetier[$codeMetier] ?? [];

            foreach ($desirees as $cle => $donnees) {
                $competence = $contexte->competencesParCode[$donnees['code_ogr']] ?? null;
                if (null === $competence) {
                    ++$resume['ignored'];
                    continue;
                }

                $liaison = $existantes[$cle] ?? null;
                if (null === $liaison) {
                    $liaison = new MetierCompetence();
                    $this->entityManager->persist($liaison);
                    ++$resume['created'];
                } else {
                    ++$resume['updated'];
                }

                $liaison
                    ->setCodeOgrMetier($metier)
                    ->setCodeOgrComp($competence)
                    ->setType($donnees['type'])
                    ->setLibelleEnjeu($donnees['libelle_enjeu'])
                    ->setCoeurMetier($donnees['coeur_metier']);
            }

            foreach ($existantes as $cle => $liaison) {
                if (!isset($desirees[$cle])) {
                    $this->entityManager->remove($liaison);
                    ++$resume['deleted'];
                }
            }
        }
    }

    /**
     * @param array<string, array<string, mixed>> $desirees
     * @param iterable<mixed>                     $groupes
     */
    private function ajouterCompetencesSouhaitees(array &$desirees, iterable $groupes, MetierCompetenceTypeEnum $type): void
    {
        foreach ($groupes as $groupe) {
            $libelleEnjeu = $this->utils->normaliserTexte($groupe['libelle'] ?? null);

            foreach (($groupe['items'] ?? []) as $item) {
                $codeOgr = $this->utils->normaliserCode($item['code_ogr'] ?? null);
                if (null === $codeOgr) {
                    continue;
                }

                $desirees[$codeOgr.'|'.$type->value] = [
                    'code_ogr' => $codeOgr,
                    'type' => $type,
                    'libelle_enjeu' => $libelleEnjeu,
                    'coeur_metier' => $this->utils->normaliserCoeurMetier($item['coeur_metier'] ?? null),
                ];
            }
        }
    }
}
