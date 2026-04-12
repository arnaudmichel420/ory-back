<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\MetierAttractiviteImportRun;
use App\Repository\MetierAttractiviteImportRunRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import-metier-attractivite-status')]
final class ImportMetierAttractiviteStatusCommand extends Command
{
    public function __construct(
        private readonly MetierAttractiviteImportRunRepository $runRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('runId', InputArgument::REQUIRED, 'Identifiant du run a consulter.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $runId = (int) $input->getArgument('runId');

        $run = $this->runRepository->find($runId);
        if (!$run instanceof MetierAttractiviteImportRun) {
            $io->error(\sprintf('Run %d introuvable.', $runId));

            return Command::FAILURE;
        }

        $io->title(\sprintf('Statut import attractivite #%d', $runId));
        $io->writeln(\sprintf('Statut : %s', $run->getStatus()));
        $io->writeln(\sprintf('Lots traites : %d / %d', $run->getProcessedBatches(), $run->getTotalBatches()));
        $io->writeln(\sprintf('Couples traites : %d / %d', $run->getProcessedPairs(), $run->getTotalPairs()));
        $io->writeln(\sprintf('Valeurs creees : %d', $run->getCreatedValues()));
        $io->writeln(\sprintf('Valeurs mises a jour : %d', $run->getUpdatedValues()));
        $io->writeln(\sprintf('Valeurs supprimees : %d', $run->getDeletedValues()));
        $io->writeln(\sprintf('Valeurs ignorees : %d', $run->getIgnoredValues()));
        $io->writeln(\sprintf('Couples en erreur : %d', $run->getErrorPairs()));

        if (null !== $run->getCompletedAt()) {
            $io->writeln(\sprintf('Termine le : %s', $run->getCompletedAt()->format(\DATE_ATOM)));
        }

        return Command::SUCCESS;
    }
}
