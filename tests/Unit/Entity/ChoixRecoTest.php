<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ChoixReco;
use App\Entity\EtudiantReponseReco;
use PHPUnit\Framework\TestCase;

class ChoixRecoTest extends TestCase
{
    public function testChoixRecoSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $choix = new ChoixReco();
        $result = $choix->setLibelle('Option A');

        $this->assertSame($choix, $result);
        $this->assertSame('Option A', $choix->getLibelle());
    }

    public function testChoixRecoValeursInitialesNulles(): void
    {
        $choix = new ChoixReco();

        $this->assertNull($choix->getId());
        $this->assertNull($choix->getLibelle());
        $this->assertNull($choix->getQuestion());
        $this->assertNull($choix->getCentreInteret());
        $this->assertNull($choix->getSecteur());
        $this->assertNull($choix->getContexteTravail());
    }

    public function testChoixRecoCollectionsInitialesSontVides(): void
    {
        $choix = new ChoixReco();

        $this->assertCount(0, $choix->getEtudiantReponseRecos());
    }

    // --- EtudiantReponseRecos ---

    public function testChoixRecoAddCollectionAjouteLElement(): void
    {
        $choix = new ChoixReco();
        $reponse = new EtudiantReponseReco();

        $choix->addEtudiantReponseReco($reponse);

        $this->assertCount(1, $choix->getEtudiantReponseRecos());
        $this->assertTrue($choix->getEtudiantReponseRecos()->contains($reponse));
    }

    public function testChoixRecoAddCollectionIgnoreLeDoublon(): void
    {
        $choix = new ChoixReco();
        $reponse = new EtudiantReponseReco();

        $choix->addEtudiantReponseReco($reponse);
        $choix->addEtudiantReponseReco($reponse);

        $this->assertCount(1, $choix->getEtudiantReponseRecos());
    }

    public function testChoixRecoAddCollectionPositionneRelationInverse(): void
    {
        $choix = new ChoixReco();
        $reponse = new EtudiantReponseReco();

        $choix->addEtudiantReponseReco($reponse);

        $this->assertSame($choix, $reponse->getChoix());
    }

    public function testChoixRecoRemoveCollectionSupprimeLElement(): void
    {
        $choix = new ChoixReco();
        $reponse = new EtudiantReponseReco();
        $choix->addEtudiantReponseReco($reponse);

        $choix->removeEtudiantReponseReco($reponse);

        $this->assertCount(0, $choix->getEtudiantReponseRecos());
    }

    public function testChoixRecoRemoveCollectionNullifyRelationInverse(): void
    {
        $choix = new ChoixReco();
        $reponse = new EtudiantReponseReco();
        $choix->addEtudiantReponseReco($reponse);

        $choix->removeEtudiantReponseReco($reponse);

        $this->assertNull($reponse->getChoix());
    }
}
