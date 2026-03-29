<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\QuestionnaireReco;
use App\Entity\QuestionReco;
use PHPUnit\Framework\TestCase;

class QuestionnaireRecoTest extends TestCase
{
    public function testQuestionnaireRecoSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $questionnaire = new QuestionnaireReco();
        $result = $questionnaire->setLibelle('Questionnaire RH');

        $this->assertSame($questionnaire, $result);
        $this->assertSame('Questionnaire RH', $questionnaire->getLibelle());
    }

    public function testQuestionnaireRecoValeursInitialesNulles(): void
    {
        $questionnaire = new QuestionnaireReco();

        $this->assertNull($questionnaire->getId());
        $this->assertNull($questionnaire->getLibelle());
        $this->assertNull($questionnaire->isActif());
    }

    public function testQuestionnaireRecoCollectionsInitialesSontVides(): void
    {
        $questionnaire = new QuestionnaireReco();

        $this->assertCount(0, $questionnaire->getQuestionRecos());
    }

    // --- QuestionRecos ---

    public function testQuestionnaireRecoAddCollectionAjouteLElement(): void
    {
        $questionnaire = new QuestionnaireReco();
        $question = new QuestionReco();

        $questionnaire->addQuestionReco($question);

        $this->assertCount(1, $questionnaire->getQuestionRecos());
        $this->assertTrue($questionnaire->getQuestionRecos()->contains($question));
    }

    public function testQuestionnaireRecoAddCollectionIgnoreLeDoublon(): void
    {
        $questionnaire = new QuestionnaireReco();
        $question = new QuestionReco();

        $questionnaire->addQuestionReco($question);
        $questionnaire->addQuestionReco($question);

        $this->assertCount(1, $questionnaire->getQuestionRecos());
    }

    public function testQuestionnaireRecoAddCollectionPositionneRelationInverse(): void
    {
        $questionnaire = new QuestionnaireReco();
        $question = new QuestionReco();

        $questionnaire->addQuestionReco($question);

        $this->assertSame($questionnaire, $question->getQuestionnaire());
    }

    public function testQuestionnaireRecoRemoveCollectionSupprimeLElement(): void
    {
        $questionnaire = new QuestionnaireReco();
        $question = new QuestionReco();
        $questionnaire->addQuestionReco($question);

        $questionnaire->removeQuestionReco($question);

        $this->assertCount(0, $questionnaire->getQuestionRecos());
    }

    public function testQuestionnaireRecoRemoveCollectionNullifyRelationInverse(): void
    {
        $questionnaire = new QuestionnaireReco();
        $question = new QuestionReco();
        $questionnaire->addQuestionReco($question);

        $questionnaire->removeQuestionReco($question);

        $this->assertNull($question->getQuestionnaire());
    }
}
