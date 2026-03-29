<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Competence;
use App\Entity\MetierCompetence;
use App\Enum\MetierCompetenceTypeEnum;
use PHPUnit\Framework\TestCase;

class CompetenceTest extends TestCase
{
    public function testCompetenceSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $competence = new Competence();
        $result = $competence->setLibelle('PHP');

        $this->assertSame($competence, $result);
        $this->assertSame('PHP', $competence->getLibelle());
    }

    public function testCompetenceValeursInitialesNulles(): void
    {
        $competence = new Competence();

        $this->assertNull($competence->getCodeOgr());
        $this->assertNull($competence->getLibelle());
        $this->assertNull($competence->getType());
        $this->assertNull($competence->isTransitionEco());
        $this->assertNull($competence->isTransitionNum());
    }

    public function testCompetenceSetTypeStockeEnum(): void
    {
        $competence = new Competence();
        $competence->setType(MetierCompetenceTypeEnum::SAVOIR);

        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR, $competence->getType());
    }

    public function testCompetenceCollectionsInitialesSontVides(): void
    {
        $competence = new Competence();

        $this->assertCount(0, $competence->getMetierCompetences());
    }

    // --- MetierCompetences ---

    public function testCompetenceAddCollectionAjouteLElement(): void
    {
        $competence = new Competence();
        $mc = new MetierCompetence();

        $competence->addMetierCompetence($mc);

        $this->assertCount(1, $competence->getMetierCompetences());
        $this->assertTrue($competence->getMetierCompetences()->contains($mc));
    }

    public function testCompetenceAddCollectionIgnoreLeDoublon(): void
    {
        $competence = new Competence();
        $mc = new MetierCompetence();

        $competence->addMetierCompetence($mc);
        $competence->addMetierCompetence($mc);

        $this->assertCount(1, $competence->getMetierCompetences());
    }

    public function testCompetenceAddCollectionPositionneRelationInverse(): void
    {
        $competence = new Competence();
        $mc = new MetierCompetence();

        $competence->addMetierCompetence($mc);

        $this->assertSame($competence, $mc->getCodeOgrComp());
    }

    public function testCompetenceRemoveCollectionSupprimeLElement(): void
    {
        $competence = new Competence();
        $mc = new MetierCompetence();
        $competence->addMetierCompetence($mc);

        $competence->removeMetierCompetence($mc);

        $this->assertCount(0, $competence->getMetierCompetences());
    }

    public function testCompetenceRemoveCollectionNullifyRelationInverse(): void
    {
        $competence = new Competence();
        $mc = new MetierCompetence();
        $competence->addMetierCompetence($mc);

        $competence->removeMetierCompetence($mc);

        $this->assertNull($mc->getCodeOgrComp());
    }
}
