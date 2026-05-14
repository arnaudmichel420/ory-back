<?php

declare(strict_types=1);

namespace App\Service;

use App\Command\SeedRecoOnboardingCommand;
use App\Entity\ChoixReco;
use App\Entity\Etudiant;
use App\Entity\EtudiantReponseReco;
use App\Entity\QuestionnaireReco;
use App\Entity\QuestionReco;
use App\Enum\QuestionRecoTypeEnum;
use App\Repository\EtudiantReponseRecoRepository;
use App\Repository\QuestionnaireRecoRepository;
use Doctrine\ORM\EntityManagerInterface;

final class RecoOnboardingService
{
    public function __construct(
        private readonly QuestionnaireRecoRepository $questionnaireRecoRepository,
        private readonly EtudiantReponseRecoRepository $etudiantReponseRecoRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function findQuestionnaireActif(): ?QuestionnaireReco
    {
        return $this->questionnaireRecoRepository->findActiveByLibelleWithQuestions(
            SeedRecoOnboardingCommand::QUESTIONNAIRE_LIBELLE,
        );
    }

    /**
     * @return array{
     *     id: int|null,
     *     libelle: string|null,
     *     questions: list<array{
     *         id: int|null,
     *         libelle: string|null,
     *         type: string|null,
     *         ordre: int|null,
     *         choix: list<array{id: int|null, libelle: string|null}>
     *     }>
     * }
     */
    public function normaliserQuestionnaire(QuestionnaireReco $questionnaire): array
    {
        $questions = $questionnaire->getQuestionRecos()->toArray();
        usort(
            $questions,
            static fn (QuestionReco $a, QuestionReco $b): int => ($a->getOrdre() ?? 0) <=> ($b->getOrdre() ?? 0),
        );

        return [
            'id' => $questionnaire->getId(),
            'libelle' => $questionnaire->getLibelle(),
            'questions' => array_map(
                static function (QuestionReco $question): array {
                    $choix = $question->getChoixRecos()->toArray();
                    usort(
                        $choix,
                        static fn (ChoixReco $a, ChoixReco $b): int => ($a->getId() ?? 0) <=> ($b->getId() ?? 0),
                    );

                    return [
                        'id' => $question->getId(),
                        'libelle' => $question->getLibelle(),
                        'type' => $question->getType()?->value,
                        'ordre' => $question->getOrdre(),
                        'choix' => array_map(
                            static fn (ChoixReco $choixReco): array => [
                                'id' => $choixReco->getId(),
                                'libelle' => $choixReco->getLibelle(),
                            ],
                            $choix,
                        ),
                    ];
                },
                $questions,
            ),
        ];
    }

    /**
     * @param list<int> $choixIds
     */
    public function remplacerReponses(Etudiant $etudiant, QuestionnaireReco $questionnaire, array $choixIds): void
    {
        $choixParId = $this->indexerChoixParId($questionnaire);
        $idsInconnus = array_values(array_diff($choixIds, array_keys($choixParId)));

        if ([] !== $idsInconnus) {
            throw new \InvalidArgumentException('Certains choix ne font pas partie du questionnaire actif.');
        }

        $this->validerQuestionsSingle($questionnaire, $choixIds, $choixParId);

        $this->etudiantReponseRecoRepository->deleteByEtudiantAndQuestionnaire($etudiant, $questionnaire);

        foreach ($choixIds as $choixId) {
            $reponse = new EtudiantReponseReco();
            $reponse
                ->setEtudiant($etudiant)
                ->setChoix($choixParId[$choixId]);

            $this->entityManager->persist($reponse);
        }

        $this->entityManager->flush();
    }

    /**
     * @return array<int, ChoixReco>
     */
    private function indexerChoixParId(QuestionnaireReco $questionnaire): array
    {
        $choixParId = [];

        foreach ($questionnaire->getQuestionRecos() as $question) {
            foreach ($question->getChoixRecos() as $choix) {
                $id = $choix->getId();
                if (null !== $id) {
                    $choixParId[$id] = $choix;
                }
            }
        }

        return $choixParId;
    }

    /**
     * @param list<int>             $choixIds
     * @param array<int, ChoixReco> $choixParId
     */
    private function validerQuestionsSingle(
        QuestionnaireReco $questionnaire,
        array $choixIds,
        array $choixParId,
    ): void {
        $selectionParQuestion = [];

        foreach ($choixIds as $choixId) {
            $question = $choixParId[$choixId]->getQuestion();
            $questionId = $question?->getId();

            if (null !== $questionId) {
                $selectionParQuestion[$questionId] = ($selectionParQuestion[$questionId] ?? 0) + 1;
            }
        }

        foreach ($questionnaire->getQuestionRecos() as $question) {
            if (QuestionRecoTypeEnum::SINGLE !== $question->getType()) {
                continue;
            }

            $questionId = $question->getId();
            if (null === $questionId) {
                continue;
            }

            if (($selectionParQuestion[$questionId] ?? 0) !== 1) {
                throw new \InvalidArgumentException(\sprintf('La question "%s" attend exactement une réponse.', $question->getLibelle()));
            }
        }
    }
}
