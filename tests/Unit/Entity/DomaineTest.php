<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Domaine;
use App\Entity\SousDomaine;
use PHPUnit\Framework\TestCase;

class DomaineTest extends TestCase
{
    public function testDomaineSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $domaine = new Domaine();
        $result = $domaine->setLibelle('Informatique');

        $this->assertSame($domaine, $result);
        $this->assertSame('Informatique', $domaine->getLibelle());
    }

    public function testDomaineValeursInitialesNulles(): void
    {
        $domaine = new Domaine();

        $this->assertNull($domaine->getId());
        $this->assertNull($domaine->getCode());
        $this->assertNull($domaine->getLibelle());
    }

    public function testDomaineCollectionsInitialesSontVides(): void
    {
        $domaine = new Domaine();

        $this->assertCount(0, $domaine->getSousDomaines());
    }

    // --- SousDomaines ---

    public function testDomaineAddCollectionAjouteLElement(): void
    {
        $domaine = new Domaine();
        $sousDomaine = new SousDomaine();

        $domaine->addSousDomaine($sousDomaine);

        $this->assertCount(1, $domaine->getSousDomaines());
        $this->assertTrue($domaine->getSousDomaines()->contains($sousDomaine));
    }

    public function testDomaineAddCollectionIgnoreLeDoublon(): void
    {
        $domaine = new Domaine();
        $sousDomaine = new SousDomaine();

        $domaine->addSousDomaine($sousDomaine);
        $domaine->addSousDomaine($sousDomaine);

        $this->assertCount(1, $domaine->getSousDomaines());
    }

    public function testDomaineAddCollectionPositionneRelationInverse(): void
    {
        $domaine = new Domaine();
        $sousDomaine = new SousDomaine();

        $domaine->addSousDomaine($sousDomaine);

        $this->assertSame($domaine, $sousDomaine->getDomaine());
    }

    public function testDomaineRemoveCollectionSupprimeLElement(): void
    {
        $domaine = new Domaine();
        $sousDomaine = new SousDomaine();
        $domaine->addSousDomaine($sousDomaine);

        $domaine->removeSousDomaine($sousDomaine);

        $this->assertCount(0, $domaine->getSousDomaines());
    }

    public function testDomaineRemoveCollectionNullifyRelationInverse(): void
    {
        $domaine = new Domaine();
        $sousDomaine = new SousDomaine();
        $domaine->addSousDomaine($sousDomaine);

        $domaine->removeSousDomaine($sousDomaine);

        $this->assertNull($sousDomaine->getDomaine());
    }
}
