<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Metier;
use App\Entity\SousDomaine;
use PHPUnit\Framework\TestCase;

class SousDomaineTest extends TestCase
{
    public function testSousDomaineSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $sd = new SousDomaine();
        $result = $sd->setLibelle('Développement web');

        $this->assertSame($sd, $result);
        $this->assertSame('Développement web', $sd->getLibelle());
    }

    public function testSousDomaineValeursInitialesNulles(): void
    {
        $sd = new SousDomaine();

        $this->assertNull($sd->getId());
        $this->assertNull($sd->getCode());
        $this->assertNull($sd->getLibelle());
        $this->assertNull($sd->getDomaine());
    }

    public function testSousDomaineCollectionsInitialesSontVides(): void
    {
        $sd = new SousDomaine();

        $this->assertCount(0, $sd->getMetiers());
    }

    // --- Metiers ---

    public function testSousDomaineAddCollectionAjouteLElement(): void
    {
        $sd = new SousDomaine();
        $metier = new Metier();

        $sd->addMetier($metier);

        $this->assertCount(1, $sd->getMetiers());
        $this->assertTrue($sd->getMetiers()->contains($metier));
    }

    public function testSousDomaineAddCollectionIgnoreLeDoublon(): void
    {
        $sd = new SousDomaine();
        $metier = new Metier();

        $sd->addMetier($metier);
        $sd->addMetier($metier);

        $this->assertCount(1, $sd->getMetiers());
    }

    public function testSousDomaineAddCollectionPositionneRelationInverse(): void
    {
        $sd = new SousDomaine();
        $metier = new Metier();

        $sd->addMetier($metier);

        $this->assertSame($sd, $metier->getSousDomaine());
    }

    public function testSousDomaineRemoveCollectionSupprimeLElement(): void
    {
        $sd = new SousDomaine();
        $metier = new Metier();
        $sd->addMetier($metier);

        $sd->removeMetier($metier);

        $this->assertCount(0, $sd->getMetiers());
    }

    public function testSousDomaineRemoveCollectionNullifyRelationInverse(): void
    {
        $sd = new SousDomaine();
        $metier = new Metier();
        $sd->addMetier($metier);

        $sd->removeMetier($metier);

        $this->assertNull($metier->getSousDomaine());
    }
}
