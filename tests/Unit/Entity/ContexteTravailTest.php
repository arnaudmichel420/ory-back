<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ChoixReco;
use App\Entity\ContexteTravail;
use App\Entity\MetierContexteTravail;
use PHPUnit\Framework\TestCase;

class ContexteTravailTest extends TestCase
{
    public function testContexteTravailSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $ct = new ContexteTravail();
        $result = $ct->setLibelle('Télétravail');

        $this->assertSame($ct, $result);
        $this->assertSame('Télétravail', $ct->getLibelle());
    }

    public function testContexteTravailValeursInitialesNulles(): void
    {
        $ct = new ContexteTravail();

        $this->assertNull($ct->getCodeOgr());
        $this->assertNull($ct->getLibelle());
        $this->assertNull($ct->getTypeContexte());
    }

    public function testContexteTravailCollectionsInitialesSontVides(): void
    {
        $ct = new ContexteTravail();

        $this->assertCount(0, $ct->getMetierContexteTravails());
        $this->assertCount(0, $ct->getChoixRecos());
    }

    // --- MetierContexteTravails ---

    public function testContexteTravailAddCollectionMetierContexteTravailAjouteLElement(): void
    {
        $ct = new ContexteTravail();
        $mct = new MetierContexteTravail();

        $ct->addMetierContexteTravail($mct);

        $this->assertCount(1, $ct->getMetierContexteTravails());
        $this->assertTrue($ct->getMetierContexteTravails()->contains($mct));
    }

    public function testContexteTravailAddCollectionMetierContexteTravailIgnoreLeDoublon(): void
    {
        $ct = new ContexteTravail();
        $mct = new MetierContexteTravail();

        $ct->addMetierContexteTravail($mct);
        $ct->addMetierContexteTravail($mct);

        $this->assertCount(1, $ct->getMetierContexteTravails());
    }

    public function testContexteTravailAddCollectionMetierContexteTravailPositionneRelationInverse(): void
    {
        $ct = new ContexteTravail();
        $mct = new MetierContexteTravail();

        $ct->addMetierContexteTravail($mct);

        $this->assertSame($ct, $mct->getCodeOgrContexte());
    }

    public function testContexteTravailRemoveCollectionMetierContexteTravailSupprimeLElement(): void
    {
        $ct = new ContexteTravail();
        $mct = new MetierContexteTravail();
        $ct->addMetierContexteTravail($mct);

        $ct->removeMetierContexteTravail($mct);

        $this->assertCount(0, $ct->getMetierContexteTravails());
    }

    public function testContexteTravailRemoveCollectionMetierContexteTravailNullifyRelationInverse(): void
    {
        $ct = new ContexteTravail();
        $mct = new MetierContexteTravail();
        $ct->addMetierContexteTravail($mct);

        $ct->removeMetierContexteTravail($mct);

        $this->assertNull($mct->getCodeOgrContexte());
    }

    // --- ChoixRecos ---

    public function testContexteTravailAddCollectionChoixRecoAjouteLElement(): void
    {
        $ct = new ContexteTravail();
        $choix = new ChoixReco();

        $ct->addChoixReco($choix);

        $this->assertCount(1, $ct->getChoixRecos());
        $this->assertTrue($ct->getChoixRecos()->contains($choix));
    }

    public function testContexteTravailAddCollectionChoixRecoIgnoreLeDoublon(): void
    {
        $ct = new ContexteTravail();
        $choix = new ChoixReco();

        $ct->addChoixReco($choix);
        $ct->addChoixReco($choix);

        $this->assertCount(1, $ct->getChoixRecos());
    }

    public function testContexteTravailAddCollectionChoixRecoPositionneRelationInverse(): void
    {
        $ct = new ContexteTravail();
        $choix = new ChoixReco();

        $ct->addChoixReco($choix);

        $this->assertSame($ct, $choix->getContexteTravail());
    }

    public function testContexteTravailRemoveCollectionChoixRecoSupprimeLElement(): void
    {
        $ct = new ContexteTravail();
        $choix = new ChoixReco();
        $ct->addChoixReco($choix);

        $ct->removeChoixReco($choix);

        $this->assertCount(0, $ct->getChoixRecos());
    }

    public function testContexteTravailRemoveCollectionChoixRecoNullifyRelationInverse(): void
    {
        $ct = new ContexteTravail();
        $choix = new ChoixReco();
        $ct->addChoixReco($choix);

        $ct->removeChoixReco($choix);

        $this->assertNull($choix->getContexteTravail());
    }
}
