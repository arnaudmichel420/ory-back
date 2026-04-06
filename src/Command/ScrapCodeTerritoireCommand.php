<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\TerritoireServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:scrap-territoire')]
final class ScrapCodeTerritoireCommand extends Command
{
    public function __construct(
        private readonly TerritoireServices $territoireServices,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->territoireServices->scrapTerritoire();

        $output->writeln(\sprintf('<info>%s</info>', $result['message']));
        $output->writeln('');
        $output->writeln('<comment>Territoires recuperes par type :</comment>');

        foreach ($result['fetched'] as $type => $count) {
            $output->writeln(\sprintf(' - %s : %d', $type, $count));
        }

        $output->writeln('');
        $output->writeln('<comment>Resultat import :</comment>');
        $output->writeln(\sprintf(' - recus : %d', $result['import']['total_received']));
        $output->writeln(\sprintf(' - crees : %d', $result['import']['created']));
        $output->writeln(\sprintf(' - mis a jour : %d', $result['import']['updated']));
        $output->writeln(\sprintf(' - invalides ignores : %d', $result['import']['skipped_invalid']));
        $output->writeln(\sprintf(' - parents relies : %d', $result['import']['parent_bound']));
        $output->writeln(\sprintf(' - parents introuvables : %d', $result['import']['parent_missing']));

        return Command::SUCCESS;
    }
}
