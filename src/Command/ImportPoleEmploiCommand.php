<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\PoleEmploiImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import-pole-emploi')]
final class ImportPoleEmploiCommand extends Command
{
    public function __construct(
        private readonly PoleEmploiImportService $poleEmploiImportService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Import France Travail');

        $resultat = $this->poleEmploiImportService->importer();

        $io->section('Referentiels');
        $this->afficherCompteurs($io, 'Domaines', $resultat['referentiels']['domaines']);
        $this->afficherCompteurs($io, 'Sous-domaines', $resultat['referentiels']['sous_domaines']);
        $this->afficherCompteurs($io, 'Centres d\'interet', $resultat['referentiels']['centres_interet']);
        $this->afficherCompteurs($io, 'Secteurs', $resultat['referentiels']['secteurs']);
        $this->afficherCompteurs($io, 'Contextes de travail', $resultat['referentiels']['contextes_travail']);
        $this->afficherCompteurs($io, 'Competences', $resultat['referentiels']['competences']);

        $io->section('Metiers');
        $this->afficherCompteurs($io, 'Metiers', $resultat['metiers']);

        $io->section('Ponts');
        $this->afficherCompteurs($io, 'Appellations', $resultat['ponts']['appellations']);
        $this->afficherCompteurs($io, 'Metier / Competence', $resultat['ponts']['metier_competences']);
        $this->afficherCompteurs($io, 'Metier / Contexte de travail', $resultat['ponts']['metier_contextes_travail']);
        $this->afficherCompteurs($io, 'Mobilites', $resultat['ponts']['mobilites']);
        $this->afficherCompteurs($io, 'Metier / Secteur', $resultat['ponts']['metier_secteurs']);
        $this->afficherCompteurs($io, 'Metier / Centre d\'interet', $resultat['ponts']['metier_centres_interet']);

        $io->success('Import France Travail termine.');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, int> $compteurs
     */
    private function afficherCompteurs(SymfonyStyle $io, string $label, array $compteurs): void
    {
        $io->writeln(\sprintf('<comment>%s</comment>', $label));

        foreach ($compteurs as $cle => $valeur) {
            $io->writeln(\sprintf(' - %s : %d', \str_replace('_', ' ', $cle), $valeur));
        }
    }
}
