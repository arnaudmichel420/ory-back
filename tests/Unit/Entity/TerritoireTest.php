<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MetierAttractivite;
use App\Entity\Territoire;
use App\Enum\TerritoireCodeTypeTerritoireEnum;
use PHPUnit\Framework\TestCase;

class TerritoireTest extends TestCase
{
    public function testTerritoireSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $territoire = new Territoire();
        $result = $territoire->setLibelleTerritoire('Île-de-France');

        $this->assertSame($territoire, $result);
        $this->assertSame('Île-de-France', $territoire->getLibelleTerritoire());
    }

    public function testTerritoireValeursInitialesNulles(): void
    {
        $territoire = new Territoire();

        $this->assertNull($territoire->getId());
        $this->assertNull($territoire->getCodeTypeTerritoire());
        $this->assertNull($territoire->getCodeTerritoire());
        $this->assertNull($territoire->getLibelleTerritoire());
        $this->assertNull($territoire->getCodeTypeTerritoireParent());
        $this->assertNull($territoire->getCodeTerritoireParent());
    }

    public function testTerritoireSetCodeTypeTerritoireStockeEnum(): void
    {
        $territoire = new Territoire();
        $territoire->setCodeTypeTerritoire(TerritoireCodeTypeTerritoireEnum::DEP);

        $this->assertSame(TerritoireCodeTypeTerritoireEnum::DEP, $territoire->getCodeTypeTerritoire());
    }

    public function testTerritoireCollectionsInitialesSontVides(): void
    {
        $territoire = new Territoire();

        $this->assertCount(0, $territoire->getTerritoires());
        $this->assertCount(0, $territoire->getMetierAttractivites());
    }

    // --- Territoires (self-référencé) ---

    public function testTerritoireAddCollectionTerritoireAjouteLElement(): void
    {
        $parent = new Territoire();
        $enfant = new Territoire();

        $parent->addTerritoire($enfant);

        $this->assertCount(1, $parent->getTerritoires());
        $this->assertTrue($parent->getTerritoires()->contains($enfant));
    }

    public function testTerritoireAddCollectionTerritoireIgnoreLeDoublon(): void
    {
        $parent = new Territoire();
        $enfant = new Territoire();

        $parent->addTerritoire($enfant);
        $parent->addTerritoire($enfant);

        $this->assertCount(1, $parent->getTerritoires());
    }

    public function testTerritoireAddCollectionTerritoirePositionneRelationInverse(): void
    {
        $parent = new Territoire();
        $enfant = new Territoire();

        $parent->addTerritoire($enfant);

        $this->assertSame($parent, $enfant->getCodeTerritoireParent());
    }

    public function testTerritoireRemoveCollectionTerritoireSupprimeLElement(): void
    {
        $parent = new Territoire();
        $enfant = new Territoire();
        $parent->addTerritoire($enfant);

        $parent->removeTerritoire($enfant);

        $this->assertCount(0, $parent->getTerritoires());
    }

    public function testTerritoireRemoveCollectionTerritoireNullifyRelationInverse(): void
    {
        $parent = new Territoire();
        $enfant = new Territoire();
        $parent->addTerritoire($enfant);

        $parent->removeTerritoire($enfant);

        $this->assertNull($enfant->getCodeTerritoireParent());
    }

    // --- MetierAttractivites ---

    public function testTerritoireAddCollectionMetierAttractiviteAjouteLElement(): void
    {
        $territoire = new Territoire();
        $ma = new MetierAttractivite();

        $territoire->addMetierAttractivite($ma);

        $this->assertCount(1, $territoire->getMetierAttractivites());
        $this->assertTrue($territoire->getMetierAttractivites()->contains($ma));
    }

    public function testTerritoireAddCollectionMetierAttractiviteIgnoreLeDoublon(): void
    {
        $territoire = new Territoire();
        $ma = new MetierAttractivite();

        $territoire->addMetierAttractivite($ma);
        $territoire->addMetierAttractivite($ma);

        $this->assertCount(1, $territoire->getMetierAttractivites());
    }

    public function testTerritoireAddCollectionMetierAttractivitePositionneRelationInverse(): void
    {
        $territoire = new Territoire();
        $ma = new MetierAttractivite();

        $territoire->addMetierAttractivite($ma);

        $this->assertSame($territoire, $ma->getTerritoire());
    }

    public function testTerritoireRemoveCollectionMetierAttractiviteSupprimeLElement(): void
    {
        $territoire = new Territoire();
        $ma = new MetierAttractivite();
        $territoire->addMetierAttractivite($ma);

        $territoire->removeMetierAttractivite($ma);

        $this->assertCount(0, $territoire->getMetierAttractivites());
    }

    public function testTerritoireRemoveCollectionMetierAttractiviteNullifyRelationInverse(): void
    {
        $territoire = new Territoire();
        $ma = new MetierAttractivite();
        $territoire->addMetierAttractivite($ma);

        $territoire->removeMetierAttractivite($ma);

        $this->assertNull($ma->getTerritoire());
    }
}
