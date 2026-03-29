<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ChoixReco;
use App\Entity\MetierSecteur;
use App\Entity\Secteur;
use PHPUnit\Framework\TestCase;

class SecteurTest extends TestCase
{
    public function testSecteurSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $secteur = new Secteur();
        $result = $secteur->setLibelle('Numérique');

        $this->assertSame($secteur, $result);
        $this->assertSame('Numérique', $secteur->getLibelle());
    }

    public function testSecteurValeursInitialesNulles(): void
    {
        $secteur = new Secteur();

        $this->assertNull($secteur->getId());
        $this->assertNull($secteur->getCode());
        $this->assertNull($secteur->getLibelle());
        $this->assertNull($secteur->getDefinition());
        $this->assertNull($secteur->getSousSecteurParent());
    }

    public function testSecteurCollectionsInitialesSontVides(): void
    {
        $secteur = new Secteur();

        $this->assertCount(0, $secteur->getSecteurs());
        $this->assertCount(0, $secteur->getMetierSecteurs());
        $this->assertCount(0, $secteur->getChoixRecos());
    }

    // --- Secteurs (self-référencé) ---

    public function testSecteurAddCollectionSecteurAjouteLElement(): void
    {
        $parent = new Secteur();
        $enfant = new Secteur();

        $parent->addSecteur($enfant);

        $this->assertCount(1, $parent->getSecteurs());
        $this->assertTrue($parent->getSecteurs()->contains($enfant));
    }

    public function testSecteurAddCollectionSecteurIgnoreLeDoublon(): void
    {
        $parent = new Secteur();
        $enfant = new Secteur();

        $parent->addSecteur($enfant);
        $parent->addSecteur($enfant);

        $this->assertCount(1, $parent->getSecteurs());
    }

    public function testSecteurAddCollectionSecteurPositionneRelationInverse(): void
    {
        $parent = new Secteur();
        $enfant = new Secteur();

        $parent->addSecteur($enfant);

        $this->assertSame($parent, $enfant->getSousSecteurParent());
    }

    public function testSecteurRemoveCollectionSecteurSupprimeLElement(): void
    {
        $parent = new Secteur();
        $enfant = new Secteur();
        $parent->addSecteur($enfant);

        $parent->removeSecteur($enfant);

        $this->assertCount(0, $parent->getSecteurs());
    }

    public function testSecteurRemoveCollectionSecteurNullifyRelationInverse(): void
    {
        $parent = new Secteur();
        $enfant = new Secteur();
        $parent->addSecteur($enfant);

        $parent->removeSecteur($enfant);

        $this->assertNull($enfant->getSousSecteurParent());
    }

    // --- MetierSecteurs ---

    public function testSecteurAddCollectionMetierSecteurAjouteLElement(): void
    {
        $secteur = new Secteur();
        $ms = new MetierSecteur();

        $secteur->addMetierSecteur($ms);

        $this->assertCount(1, $secteur->getMetierSecteurs());
        $this->assertTrue($secteur->getMetierSecteurs()->contains($ms));
    }

    public function testSecteurAddCollectionMetierSecteurIgnoreLeDoublon(): void
    {
        $secteur = new Secteur();
        $ms = new MetierSecteur();

        $secteur->addMetierSecteur($ms);
        $secteur->addMetierSecteur($ms);

        $this->assertCount(1, $secteur->getMetierSecteurs());
    }

    public function testSecteurAddCollectionMetierSecteurPositionneRelationInverse(): void
    {
        $secteur = new Secteur();
        $ms = new MetierSecteur();

        $secteur->addMetierSecteur($ms);

        $this->assertSame($secteur, $ms->getSecteur());
    }

    public function testSecteurRemoveCollectionMetierSecteurSupprimeLElement(): void
    {
        $secteur = new Secteur();
        $ms = new MetierSecteur();
        $secteur->addMetierSecteur($ms);

        $secteur->removeMetierSecteur($ms);

        $this->assertCount(0, $secteur->getMetierSecteurs());
    }

    public function testSecteurRemoveCollectionMetierSecteurNullifyRelationInverse(): void
    {
        $secteur = new Secteur();
        $ms = new MetierSecteur();
        $secteur->addMetierSecteur($ms);

        $secteur->removeMetierSecteur($ms);

        $this->assertNull($ms->getSecteur());
    }

    // --- ChoixRecos ---

    public function testSecteurAddCollectionChoixRecoAjouteLElement(): void
    {
        $secteur = new Secteur();
        $choix = new ChoixReco();

        $secteur->addChoixReco($choix);

        $this->assertCount(1, $secteur->getChoixRecos());
        $this->assertTrue($secteur->getChoixRecos()->contains($choix));
    }

    public function testSecteurAddCollectionChoixRecoIgnoreLeDoublon(): void
    {
        $secteur = new Secteur();
        $choix = new ChoixReco();

        $secteur->addChoixReco($choix);
        $secteur->addChoixReco($choix);

        $this->assertCount(1, $secteur->getChoixRecos());
    }

    public function testSecteurAddCollectionChoixRecoPositionneRelationInverse(): void
    {
        $secteur = new Secteur();
        $choix = new ChoixReco();

        $secteur->addChoixReco($choix);

        $this->assertSame($secteur, $choix->getSecteur());
    }

    public function testSecteurRemoveCollectionChoixRecoSupprimeLElement(): void
    {
        $secteur = new Secteur();
        $choix = new ChoixReco();
        $secteur->addChoixReco($choix);

        $secteur->removeChoixReco($choix);

        $this->assertCount(0, $secteur->getChoixRecos());
    }

    public function testSecteurRemoveCollectionChoixRecoNullifyRelationInverse(): void
    {
        $secteur = new Secteur();
        $choix = new ChoixReco();
        $secteur->addChoixReco($choix);

        $secteur->removeChoixReco($choix);

        $this->assertNull($choix->getSecteur());
    }
}
