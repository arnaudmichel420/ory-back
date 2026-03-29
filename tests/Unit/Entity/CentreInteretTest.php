<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\CentreInteret;
use App\Entity\ChoixReco;
use App\Entity\MetierCentreInteret;
use PHPUnit\Framework\TestCase;

class CentreInteretTest extends TestCase
{
    public function testCentreInteretSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $ci = new CentreInteret();
        $result = $ci->setLibelle('Sport');

        $this->assertSame($ci, $result);
        $this->assertSame('Sport', $ci->getLibelle());
    }

    public function testCentreInteretValeursInitialesNulles(): void
    {
        $ci = new CentreInteret();

        $this->assertNull($ci->getId());
        $this->assertNull($ci->getLibelle());
        $this->assertNull($ci->getDefinition());
    }

    public function testCentreInteretCollectionsInitialesSontVides(): void
    {
        $ci = new CentreInteret();

        $this->assertCount(0, $ci->getMetierCentreInterets());
        $this->assertCount(0, $ci->getChoixRecos());
    }

    // --- MetierCentreInterets ---

    public function testCentreInteretAddCollectionMetierCentreInteretAjouteLElement(): void
    {
        $ci = new CentreInteret();
        $mci = new MetierCentreInteret();

        $ci->addMetierCentreInteret($mci);

        $this->assertCount(1, $ci->getMetierCentreInterets());
        $this->assertTrue($ci->getMetierCentreInterets()->contains($mci));
    }

    public function testCentreInteretAddCollectionMetierCentreInteretIgnoreLeDoublon(): void
    {
        $ci = new CentreInteret();
        $mci = new MetierCentreInteret();

        $ci->addMetierCentreInteret($mci);
        $ci->addMetierCentreInteret($mci);

        $this->assertCount(1, $ci->getMetierCentreInterets());
    }

    public function testCentreInteretAddCollectionMetierCentreInteretPositionneRelationInverse(): void
    {
        $ci = new CentreInteret();
        $mci = new MetierCentreInteret();

        $ci->addMetierCentreInteret($mci);

        $this->assertSame($ci, $mci->getCentreInteret());
    }

    public function testCentreInteretRemoveCollectionMetierCentreInteretSupprimeLElement(): void
    {
        $ci = new CentreInteret();
        $mci = new MetierCentreInteret();
        $ci->addMetierCentreInteret($mci);

        $ci->removeMetierCentreInteret($mci);

        $this->assertCount(0, $ci->getMetierCentreInterets());
    }

    public function testCentreInteretRemoveCollectionMetierCentreInteretNullifyRelationInverse(): void
    {
        $ci = new CentreInteret();
        $mci = new MetierCentreInteret();
        $ci->addMetierCentreInteret($mci);

        $ci->removeMetierCentreInteret($mci);

        $this->assertNull($mci->getCentreInteret());
    }

    // --- ChoixRecos ---

    public function testCentreInteretAddCollectionChoixRecoAjouteLElement(): void
    {
        $ci = new CentreInteret();
        $choix = new ChoixReco();

        $ci->addChoixReco($choix);

        $this->assertCount(1, $ci->getChoixRecos());
        $this->assertTrue($ci->getChoixRecos()->contains($choix));
    }

    public function testCentreInteretAddCollectionChoixRecoIgnoreLeDoublon(): void
    {
        $ci = new CentreInteret();
        $choix = new ChoixReco();

        $ci->addChoixReco($choix);
        $ci->addChoixReco($choix);

        $this->assertCount(1, $ci->getChoixRecos());
    }

    public function testCentreInteretAddCollectionChoixRecoPositionneRelationInverse(): void
    {
        $ci = new CentreInteret();
        $choix = new ChoixReco();

        $ci->addChoixReco($choix);

        $this->assertSame($ci, $choix->getCentreInteret());
    }

    public function testCentreInteretRemoveCollectionChoixRecoSupprimeLElement(): void
    {
        $ci = new CentreInteret();
        $choix = new ChoixReco();
        $ci->addChoixReco($choix);

        $ci->removeChoixReco($choix);

        $this->assertCount(0, $ci->getChoixRecos());
    }

    public function testCentreInteretRemoveCollectionChoixRecoNullifyRelationInverse(): void
    {
        $ci = new CentreInteret();
        $choix = new ChoixReco();
        $ci->addChoixReco($choix);

        $ci->removeChoixReco($choix);

        $this->assertNull($choix->getCentreInteret());
    }
}
