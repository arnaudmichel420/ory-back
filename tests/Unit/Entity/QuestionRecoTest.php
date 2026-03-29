<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ChoixReco;
use App\Entity\QuestionReco;
use App\Enum\QuestionRecoTypeEnum;
use PHPUnit\Framework\TestCase;

class QuestionRecoTest extends TestCase
{
    public function testQuestionRecoSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $question = new QuestionReco();
        $result = $question->setLibelle('Quel est votre secteur ?');

        $this->assertSame($question, $result);
        $this->assertSame('Quel est votre secteur ?', $question->getLibelle());
    }

    public function testQuestionRecoValeursInitialesNulles(): void
    {
        $question = new QuestionReco();

        $this->assertNull($question->getId());
        $this->assertNull($question->getQuestionnaire());
        $this->assertNull($question->getLibelle());
        $this->assertNull($question->getType());
        $this->assertNull($question->getOrdre());
    }

    public function testQuestionRecoSetTypeStockeEnum(): void
    {
        $question = new QuestionReco();
        $question->setType(QuestionRecoTypeEnum::SINGLE);

        $this->assertSame(QuestionRecoTypeEnum::SINGLE, $question->getType());
    }

    public function testQuestionRecoCollectionsInitialesSontVides(): void
    {
        $question = new QuestionReco();

        $this->assertCount(0, $question->getChoixRecos());
    }

    // --- ChoixRecos ---

    public function testQuestionRecoAddCollectionAjouteLElement(): void
    {
        $question = new QuestionReco();
        $choix = new ChoixReco();

        $question->addChoixReco($choix);

        $this->assertCount(1, $question->getChoixRecos());
        $this->assertTrue($question->getChoixRecos()->contains($choix));
    }

    public function testQuestionRecoAddCollectionIgnoreLeDoublon(): void
    {
        $question = new QuestionReco();
        $choix = new ChoixReco();

        $question->addChoixReco($choix);
        $question->addChoixReco($choix);

        $this->assertCount(1, $question->getChoixRecos());
    }

    public function testQuestionRecoAddCollectionPositionneRelationInverse(): void
    {
        $question = new QuestionReco();
        $choix = new ChoixReco();

        $question->addChoixReco($choix);

        $this->assertSame($question, $choix->getQuestion());
    }

    public function testQuestionRecoRemoveCollectionSupprimeLElement(): void
    {
        $question = new QuestionReco();
        $choix = new ChoixReco();
        $question->addChoixReco($choix);

        $question->removeChoixReco($choix);

        $this->assertCount(0, $question->getChoixRecos());
    }

    public function testQuestionRecoRemoveCollectionNullifyRelationInverse(): void
    {
        $question = new QuestionReco();
        $choix = new ChoixReco();
        $question->addChoixReco($choix);

        $question->removeChoixReco($choix);

        $this->assertNull($choix->getQuestion());
    }
}
