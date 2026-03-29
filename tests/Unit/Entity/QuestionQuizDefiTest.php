<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ChoixQuizDefi;
use App\Entity\QuestionQuizDefi;
use App\Enum\QuestionQuizDefiTypeEnum;
use PHPUnit\Framework\TestCase;

class QuestionQuizDefiTest extends TestCase
{
    public function testQuestionQuizDefiSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $question = new QuestionQuizDefi();
        $result = $question->setQuestion('Quelle est la réponse ?');

        $this->assertSame($question, $result);
        $this->assertSame('Quelle est la réponse ?', $question->getQuestion());
    }

    public function testQuestionQuizDefiValeursInitialesNulles(): void
    {
        $question = new QuestionQuizDefi();

        $this->assertNull($question->getId());
        $this->assertNull($question->getQuiz());
        $this->assertNull($question->getQuestion());
        $this->assertNull($question->getExplication());
        $this->assertNull($question->getType());
        $this->assertNull($question->getOrdre());
    }

    public function testQuestionQuizDefiSetTypeStockeEnum(): void
    {
        $question = new QuestionQuizDefi();
        $question->setType(QuestionQuizDefiTypeEnum::SIMPLE);

        $this->assertSame(QuestionQuizDefiTypeEnum::SIMPLE, $question->getType());
    }

    public function testQuestionQuizDefiCollectionsInitialesSontVides(): void
    {
        $question = new QuestionQuizDefi();

        $this->assertCount(0, $question->getChoixQuizDefis());
    }

    // --- ChoixQuizDefis ---

    public function testQuestionQuizDefiAddCollectionAjouteLElement(): void
    {
        $question = new QuestionQuizDefi();
        $choix = new ChoixQuizDefi();

        $question->addChoixQuizDefi($choix);

        $this->assertCount(1, $question->getChoixQuizDefis());
        $this->assertTrue($question->getChoixQuizDefis()->contains($choix));
    }

    public function testQuestionQuizDefiAddCollectionIgnoreLeDoublon(): void
    {
        $question = new QuestionQuizDefi();
        $choix = new ChoixQuizDefi();

        $question->addChoixQuizDefi($choix);
        $question->addChoixQuizDefi($choix);

        $this->assertCount(1, $question->getChoixQuizDefis());
    }

    public function testQuestionQuizDefiAddCollectionPositionneRelationInverse(): void
    {
        $question = new QuestionQuizDefi();
        $choix = new ChoixQuizDefi();

        $question->addChoixQuizDefi($choix);

        $this->assertSame($question, $choix->getQuestionQuizz());
    }

    public function testQuestionQuizDefiRemoveCollectionSupprimeLElement(): void
    {
        $question = new QuestionQuizDefi();
        $choix = new ChoixQuizDefi();
        $question->addChoixQuizDefi($choix);

        $question->removeChoixQuizDefi($choix);

        $this->assertCount(0, $question->getChoixQuizDefis());
    }

    public function testQuestionQuizDefiRemoveCollectionNullifyRelationInverse(): void
    {
        $question = new QuestionQuizDefi();
        $choix = new ChoixQuizDefi();
        $question->addChoixQuizDefi($choix);

        $question->removeChoixQuizDefi($choix);

        $this->assertNull($choix->getQuestionQuizz());
    }
}
