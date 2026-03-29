<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ActionDefi;
use App\Entity\Collectionnable;
use App\Entity\Defi;
use App\Entity\EtudiantDefi;
use App\Entity\QuizDefi;
use App\Enum\DefiTypeEnum;
use PHPUnit\Framework\TestCase;

class DefiTest extends TestCase
{
    public function testDefiSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $defi = new Defi();
        $result = $defi->setNom('Défi PHP');

        $this->assertSame($defi, $result);
        $this->assertSame('Défi PHP', $defi->getNom());
    }

    public function testDefiValeursInitialesNulles(): void
    {
        $defi = new Defi();

        $this->assertNull($defi->getId());
        $this->assertNull($defi->getNom());
        $this->assertNull($defi->getDescription());
        $this->assertNull($defi->getType());
        $this->assertNull($defi->isEstActif());
        $this->assertNull($defi->getPrerequis());
    }

    public function testDefiSetTypeStockeEnum(): void
    {
        $defi = new Defi();
        $defi->setType(DefiTypeEnum::ACTION);

        $this->assertSame(DefiTypeEnum::ACTION, $defi->getType());
    }

    public function testDefiCollectionsInitialesSontVides(): void
    {
        $defi = new Defi();

        $this->assertCount(0, $defi->getActionDefis());
        $this->assertCount(0, $defi->getQuizDefis());
        $this->assertCount(0, $defi->getCollectionnables());
        $this->assertCount(0, $defi->getEtudiantDefis());
        $this->assertCount(0, $defi->getDefisPrerequis());
    }

    // --- ActionDefis ---

    public function testDefiAddCollectionActionDefiAjouteLElement(): void
    {
        $defi = new Defi();
        $action = new ActionDefi();

        $defi->addActionDefi($action);

        $this->assertCount(1, $defi->getActionDefis());
        $this->assertTrue($defi->getActionDefis()->contains($action));
    }

    public function testDefiAddCollectionActionDefiIgnoreLeDoublon(): void
    {
        $defi = new Defi();
        $action = new ActionDefi();

        $defi->addActionDefi($action);
        $defi->addActionDefi($action);

        $this->assertCount(1, $defi->getActionDefis());
    }

    public function testDefiAddCollectionActionDefiPositionneRelationInverse(): void
    {
        $defi = new Defi();
        $action = new ActionDefi();

        $defi->addActionDefi($action);

        $this->assertSame($defi, $action->getDefi());
    }

    public function testDefiRemoveCollectionActionDefiSupprimeLElement(): void
    {
        $defi = new Defi();
        $action = new ActionDefi();
        $defi->addActionDefi($action);

        $defi->removeActionDefi($action);

        $this->assertCount(0, $defi->getActionDefis());
    }

    public function testDefiRemoveCollectionActionDefiNullifyRelationInverse(): void
    {
        $defi = new Defi();
        $action = new ActionDefi();
        $defi->addActionDefi($action);

        $defi->removeActionDefi($action);

        $this->assertNull($action->getDefi());
    }

    // --- QuizDefis ---

    public function testDefiAddCollectionQuizDefiAjouteLElement(): void
    {
        $defi = new Defi();
        $quiz = new QuizDefi();

        $defi->addQuizDefi($quiz);

        $this->assertCount(1, $defi->getQuizDefis());
        $this->assertTrue($defi->getQuizDefis()->contains($quiz));
    }

    public function testDefiAddCollectionQuizDefiIgnoreLeDoublon(): void
    {
        $defi = new Defi();
        $quiz = new QuizDefi();

        $defi->addQuizDefi($quiz);
        $defi->addQuizDefi($quiz);

        $this->assertCount(1, $defi->getQuizDefis());
    }

    public function testDefiAddCollectionQuizDefiPositionneRelationInverse(): void
    {
        $defi = new Defi();
        $quiz = new QuizDefi();

        $defi->addQuizDefi($quiz);

        $this->assertSame($defi, $quiz->getDefi());
    }

    public function testDefiRemoveCollectionQuizDefiSupprimeLElement(): void
    {
        $defi = new Defi();
        $quiz = new QuizDefi();
        $defi->addQuizDefi($quiz);

        $defi->removeQuizDefi($quiz);

        $this->assertCount(0, $defi->getQuizDefis());
    }

    public function testDefiRemoveCollectionQuizDefiNullifyRelationInverse(): void
    {
        $defi = new Defi();
        $quiz = new QuizDefi();
        $defi->addQuizDefi($quiz);

        $defi->removeQuizDefi($quiz);

        $this->assertNull($quiz->getDefi());
    }

    // --- Collectionnables (ManyToMany, relation inverse via addDefi) ---

    public function testDefiAddCollectionCollectionnableAjouteLElement(): void
    {
        $defi = new Defi();
        $collectionnable = new Collectionnable();

        $defi->addCollectionnable($collectionnable);

        $this->assertCount(1, $defi->getCollectionnables());
        $this->assertTrue($defi->getCollectionnables()->contains($collectionnable));
    }

    public function testDefiAddCollectionCollectionnableIgnoreLeDoublon(): void
    {
        $defi = new Defi();
        $collectionnable = new Collectionnable();

        $defi->addCollectionnable($collectionnable);
        $defi->addCollectionnable($collectionnable);

        $this->assertCount(1, $defi->getCollectionnables());
    }

    public function testDefiAddCollectionCollectionnablePositionneRelationInverse(): void
    {
        $defi = new Defi();
        $collectionnable = new Collectionnable();

        $defi->addCollectionnable($collectionnable);

        $this->assertTrue($collectionnable->getDefi()->contains($defi));
    }

    public function testDefiRemoveCollectionCollectionnableSupprimeLElement(): void
    {
        $defi = new Defi();
        $collectionnable = new Collectionnable();
        $defi->addCollectionnable($collectionnable);

        $defi->removeCollectionnable($collectionnable);

        $this->assertCount(0, $defi->getCollectionnables());
    }

    public function testDefiRemoveCollectionCollectionnableSupprimeCoteInverse(): void
    {
        $defi = new Defi();
        $collectionnable = new Collectionnable();
        $defi->addCollectionnable($collectionnable);

        $defi->removeCollectionnable($collectionnable);

        $this->assertFalse($collectionnable->getDefi()->contains($defi));
    }

    // --- EtudiantDefis ---

    public function testDefiAddCollectionEtudiantDefiAjouteLElement(): void
    {
        $defi = new Defi();
        $ed = new EtudiantDefi();

        $defi->addEtudiantDefi($ed);

        $this->assertCount(1, $defi->getEtudiantDefis());
        $this->assertTrue($defi->getEtudiantDefis()->contains($ed));
    }

    public function testDefiAddCollectionEtudiantDefiIgnoreLeDoublon(): void
    {
        $defi = new Defi();
        $ed = new EtudiantDefi();

        $defi->addEtudiantDefi($ed);
        $defi->addEtudiantDefi($ed);

        $this->assertCount(1, $defi->getEtudiantDefis());
    }

    public function testDefiAddCollectionEtudiantDefiPositionneRelationInverse(): void
    {
        $defi = new Defi();
        $ed = new EtudiantDefi();

        $defi->addEtudiantDefi($ed);

        $this->assertSame($defi, $ed->getDefi());
    }

    public function testDefiRemoveCollectionEtudiantDefiSupprimeLElement(): void
    {
        $defi = new Defi();
        $ed = new EtudiantDefi();
        $defi->addEtudiantDefi($ed);

        $defi->removeEtudiantDefi($ed);

        $this->assertCount(0, $defi->getEtudiantDefis());
    }

    public function testDefiRemoveCollectionEtudiantDefiNullifyRelationInverse(): void
    {
        $defi = new Defi();
        $ed = new EtudiantDefi();
        $defi->addEtudiantDefi($ed);

        $defi->removeEtudiantDefi($ed);

        $this->assertNull($ed->getDefi());
    }

    // --- DefisPrerequis (self-référencé) ---

    public function testDefiAddCollectionDefisPrerequiAjouteLElement(): void
    {
        $parent = new Defi();
        $enfant = new Defi();

        $parent->addDefisPrerequi($enfant);

        $this->assertCount(1, $parent->getDefisPrerequis());
        $this->assertTrue($parent->getDefisPrerequis()->contains($enfant));
    }

    public function testDefiAddCollectionDefisPrerequiIgnoreLeDoublon(): void
    {
        $parent = new Defi();
        $enfant = new Defi();

        $parent->addDefisPrerequi($enfant);
        $parent->addDefisPrerequi($enfant);

        $this->assertCount(1, $parent->getDefisPrerequis());
    }

    public function testDefiAddCollectionDefisPrerequiPositionneRelationInverse(): void
    {
        $parent = new Defi();
        $enfant = new Defi();

        $parent->addDefisPrerequi($enfant);

        $this->assertSame($parent, $enfant->getPrerequis());
    }

    public function testDefiRemoveCollectionDefisPrerequiSupprimeLElement(): void
    {
        $parent = new Defi();
        $enfant = new Defi();
        $parent->addDefisPrerequi($enfant);

        $parent->removeDefisPrerequi($enfant);

        $this->assertCount(0, $parent->getDefisPrerequis());
    }

    public function testDefiRemoveCollectionDefisPrerequiNullifyRelationInverse(): void
    {
        $parent = new Defi();
        $enfant = new Defi();
        $parent->addDefisPrerequi($enfant);

        $parent->removeDefisPrerequi($enfant);

        $this->assertNull($enfant->getPrerequis());
    }
}
