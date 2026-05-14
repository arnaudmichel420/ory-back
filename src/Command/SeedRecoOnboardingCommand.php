<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\CentreInteret;
use App\Entity\ChoixReco;
use App\Entity\ContexteTravail;
use App\Entity\QuestionnaireReco;
use App\Entity\QuestionReco;
use App\Entity\Secteur;
use App\Enum\QuestionRecoTypeEnum;
use App\Repository\CentreInteretRepository;
use App\Repository\ContexteTravailRepository;
use App\Repository\QuestionnaireRecoRepository;
use App\Repository\SecteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed-reco-onboarding')]
final class SeedRecoOnboardingCommand extends Command
{
    public const QUESTIONNAIRE_LIBELLE = 'Onboarding recommandation métiers';

    private const DATA_FILE = '/../../data/reco_onboarding.php';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly QuestionnaireRecoRepository $questionnaireRecoRepository,
        private readonly CentreInteretRepository $centreInteretRepository,
        private readonly SecteurRepository $secteurRepository,
        private readonly ContexteTravailRepository $contexteTravailRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Seed onboarding recommandation');

        $data = $this->chargerData();
        $centresInteret = $this->indexerCentresInteret();
        $secteurs = $this->indexerSecteurs();
        $contextesTravail = $this->indexerContextesTravail();

        $questionnaire = $this->questionnaireRecoRepository->findOneBy([
            'libelle' => $data['libelle'],
        ]);

        if (!$questionnaire instanceof QuestionnaireReco) {
            $questionnaire = new QuestionnaireReco();
            $this->entityManager->persist($questionnaire);
        }

        $questionnaire
            ->setLibelle($data['libelle'])
            ->setActif(true);

        foreach ($data['questions'] as $questionData) {
            $question = $this->findQuestionByOrdre($questionnaire, $questionData['ordre']);
            if (!$question instanceof QuestionReco) {
                $question = new QuestionReco();
                $questionnaire->addQuestionReco($question);
                $this->entityManager->persist($question);
            }

            $question
                ->setLibelle($questionData['libelle'])
                ->setType($questionData['type'])
                ->setOrdre($questionData['ordre']);

            foreach ($questionData['choix'] as $choixData) {
                $choix = $this->findChoixByLibelle($question, $choixData['libelle']);
                if (!$choix instanceof ChoixReco) {
                    $choix = new ChoixReco();
                    $question->addChoixReco($choix);
                    $this->entityManager->persist($choix);
                }

                $choix
                    ->setLibelle($choixData['libelle'])
                    ->setCentreInteret($this->resolveCentreInteret($choixData['centreInteretId'] ?? null, $centresInteret))
                    ->setSecteur($this->resolveSecteur($choixData['secteurCode'] ?? null, $secteurs))
                    ->setContexteTravail($this->resolveContexteTravail($choixData['contexteTravailCodeOgr'] ?? null, $contextesTravail));
            }
        }

        $this->entityManager->flush();

        $io->success('Questionnaire onboarding recommandation généré.');

        return Command::SUCCESS;
    }

    /**
     * @return array{
     *     libelle: string,
     *     questions: list<array{
     *         ordre: int,
     *         libelle: string,
     *         type: QuestionRecoTypeEnum,
     *         choix: list<array{
     *             libelle: string,
     *             centreInteretId?: int,
     *             secteurCode?: string,
     *             contexteTravailCodeOgr?: string
     *         }>
     *     }>
     * }
     */
    private function chargerData(): array
    {
        $dataFile = __DIR__.self::DATA_FILE;
        if (!is_file($dataFile)) {
            throw new \RuntimeException(\sprintf('Fichier de seed introuvable : %s.', $dataFile));
        }

        $data = require $dataFile;
        if (!\is_array($data)) {
            throw new \RuntimeException(\sprintf('Le fichier %s doit retourner un tableau.', $dataFile));
        }

        return $data;
    }

    /**
     * @return array<int, CentreInteret>
     */
    private function indexerCentresInteret(): array
    {
        $index = [];

        foreach ($this->centreInteretRepository->findAll() as $centreInteret) {
            $id = $centreInteret->getId();
            if (null !== $id) {
                $index[$id] = $centreInteret;
            }
        }

        return $index;
    }

    /**
     * @return array<string, Secteur>
     */
    private function indexerSecteurs(): array
    {
        $index = [];

        foreach ($this->secteurRepository->findAll() as $secteur) {
            $code = $secteur->getCode();
            if (null !== $code) {
                $index[$code] = $secteur;
            }
        }

        return $index;
    }

    /**
     * @return array<string, ContexteTravail>
     */
    private function indexerContextesTravail(): array
    {
        $index = [];

        foreach ($this->contexteTravailRepository->findAll() as $contexteTravail) {
            $code = $contexteTravail->getCodeOgr();
            if (null !== $code) {
                $index[$code] = $contexteTravail;
            }
        }

        return $index;
    }

    private function findQuestionByOrdre(QuestionnaireReco $questionnaire, int $ordre): ?QuestionReco
    {
        foreach ($questionnaire->getQuestionRecos() as $question) {
            if ($question->getOrdre() === $ordre) {
                return $question;
            }
        }

        return null;
    }

    private function findChoixByLibelle(QuestionReco $question, string $libelle): ?ChoixReco
    {
        foreach ($question->getChoixRecos() as $choix) {
            if ($choix->getLibelle() === $libelle) {
                return $choix;
            }
        }

        return null;
    }

    /**
     * @param array<int, CentreInteret> $centresInteret
     */
    private function resolveCentreInteret(?int $id, array $centresInteret): ?CentreInteret
    {
        if (null === $id) {
            return null;
        }

        if (isset($centresInteret[$id])) {
            return $centresInteret[$id];
        }

        throw new \RuntimeException(\sprintf('Centre d\'intérêt introuvable : %d.', $id));
    }

    /**
     * @param array<string, Secteur> $secteurs
     */
    private function resolveSecteur(?string $code, array $secteurs): ?Secteur
    {
        if (null === $code) {
            return null;
        }

        if (isset($secteurs[$code])) {
            return $secteurs[$code];
        }

        throw new \RuntimeException(\sprintf('Secteur introuvable : %s.', $code));
    }

    /**
     * @param array<string, ContexteTravail> $contextesTravail
     */
    private function resolveContexteTravail(?string $code, array $contextesTravail): ?ContexteTravail
    {
        if (null === $code) {
            return null;
        }

        if (isset($contextesTravail[$code])) {
            return $contextesTravail[$code];
        }

        throw new \RuntimeException(\sprintf('Contexte de travail introuvable : %s.', $code));
    }
}
