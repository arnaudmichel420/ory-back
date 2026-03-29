<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\QuestionQuizDefi;
use App\Entity\QuizDefi;
use PHPUnit\Framework\TestCase;

class QuizDefiTest extends TestCase
{
    public function testQuizDefiSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $quiz = new QuizDefi();
        $result = $quiz->setNom('Quiz PHP');

        $this->assertSame($quiz, $result);
        $this->assertSame('Quiz PHP', $quiz->getNom());
    }

    public function testQuizDefiValeursInitialesNulles(): void
    {
        $quiz = new QuizDefi();

        $this->assertNull($quiz->getId());
        $this->assertNull($quiz->getDefi());
        $this->assertNull($quiz->getNom());
        $this->assertNull($quiz->getDescription());
    }

    public function testQuizDefiCollectionsInitialesSontVides(): void
    {
        $quiz = new QuizDefi();

        $this->assertCount(0, $quiz->getQuestionQuizDefis());
    }

    // --- QuestionQuizDefis ---

    public function testQuizDefiAddCollectionAjouteLElement(): void
    {
        $quiz = new QuizDefi();
        $question = new QuestionQuizDefi();

        $quiz->addQuestionQuizDefi($question);

        $this->assertCount(1, $quiz->getQuestionQuizDefis());
        $this->assertTrue($quiz->getQuestionQuizDefis()->contains($question));
    }

    public function testQuizDefiAddCollectionIgnoreLeDoublon(): void
    {
        $quiz = new QuizDefi();
        $question = new QuestionQuizDefi();

        $quiz->addQuestionQuizDefi($question);
        $quiz->addQuestionQuizDefi($question);

        $this->assertCount(1, $quiz->getQuestionQuizDefis());
    }

    public function testQuizDefiAddCollectionPositionneRelationInverse(): void
    {
        $quiz = new QuizDefi();
        $question = new QuestionQuizDefi();

        $quiz->addQuestionQuizDefi($question);

        $this->assertSame($quiz, $question->getQuiz());
    }

    public function testQuizDefiRemoveCollectionSupprimeLElement(): void
    {
        $quiz = new QuizDefi();
        $question = new QuestionQuizDefi();
        $quiz->addQuestionQuizDefi($question);

        $quiz->removeQuestionQuizDefi($question);

        $this->assertCount(0, $quiz->getQuestionQuizDefis());
    }

    public function testQuizDefiRemoveCollectionNullifyRelationInverse(): void
    {
        $quiz = new QuizDefi();
        $question = new QuestionQuizDefi();
        $quiz->addQuestionQuizDefi($question);

        $quiz->removeQuestionQuizDefi($question);

        $this->assertNull($question->getQuiz());
    }
}
