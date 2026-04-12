<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\MetierAttractiviteImportRun;
use App\Enum\TerritoireCodeTypeTerritoireEnum;
use App\Message\ImportMetierAttractiviteMessage;
use App\Repository\MetierRepository;
use App\Repository\TerritoireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:import-metier-attractivite')]
final class ImportMetierAttractiviteCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus,
        private readonly MetierRepository $metierRepository,
        private readonly TerritoireRepository $territoireRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('batch-size', null, InputOption::VALUE_REQUIRED, 'Nombre de couples par message.', '500');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $batchSize = max(1, (int) $input->getOption('batch-size'));

        $departementCodes = $this->territoireRepository->findCodesByType(TerritoireCodeTypeTerritoireEnum::DEP);
        $metiers = $this->metierRepository->findAttractiviteImportCandidates();
        $departementCount = \count($departementCodes);
        $metierCount = \count($metiers);
        $totalPairs = $departementCount * $metierCount;

        $run = new MetierAttractiviteImportRun();
        $run
            ->setTotalPairs($totalPairs)
            ->setTotalBatches((int) ceil($totalPairs / $batchSize));

        if (0 === $totalPairs) {
            $run->finalize();
        }

        $this->entityManager->persist($run);
        $this->entityManager->flush();

        $runId = $run->getId();
        if (null === $runId) {
            throw new \RuntimeException('Impossible de recuperer l\'identifiant du run d\'import attractivite.');
        }

        $batch = [];
        $dispatchedBatches = 0;

        foreach ($departementCodes as $codeDepartement) {
            foreach ($metiers as $metier) {
                $batch[] = [
                    'codeRome' => $metier['codeRome'],
                    'codeOgrMetier' => $metier['codeOgr'],
                    'codeDepartement' => $codeDepartement,
                ];

                if (\count($batch) < $batchSize) {
                    continue;
                }

                $this->messageBus->dispatch(new ImportMetierAttractiviteMessage($runId, $batch));
                $batch = [];
                ++$dispatchedBatches;
            }
        }

        if ([] !== $batch) {
            $this->messageBus->dispatch(new ImportMetierAttractiviteMessage($runId, $batch));
            ++$dispatchedBatches;
        }

        $io->title('Import attractivite metiers');
        $io->success('Les messages ont ete dispatches sur Messenger.');
        $io->writeln(\sprintf('Run id : %d', $runId));
        $io->writeln(\sprintf('Departements : %d', $departementCount));
        $io->writeln(\sprintf('Metiers parents : %d', $metierCount));
        $io->writeln(\sprintf('Couples dispatches : %d', $totalPairs));
        $io->writeln(\sprintf('Lots dispatches : %d', $dispatchedBatches));
        $io->writeln(\sprintf('Batch size : %d', $batchSize));
        $io->writeln('En environnement dev, preferer `php bin/console --no-debug app:import-metier-attractivite` pour eviter la surcharge memoire du debug Doctrine.');
        $io->writeln('Lancer ensuite `php bin/console messenger:consume async` pour traiter la file.');

        return Command::SUCCESS;
    }
}
