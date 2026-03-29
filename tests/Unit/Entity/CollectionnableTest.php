<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Collectionnable;
use App\Entity\Defi;
use App\Entity\Etudiant;
use PHPUnit\Framework\TestCase;

class CollectionnableTest extends TestCase
{
    public function testCollectionnableSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $c = new Collectionnable();
        $result = $c->setLibelle('Badge PHP');

        $this->assertSame($c, $result);
        $this->assertSame('Badge PHP', $c->getLibelle());
    }

    public function testCollectionnableValeursInitialesNulles(): void
    {
        $c = new Collectionnable();

        $this->assertNull($c->getId());
        $this->assertNull($c->getLibelle());
        $this->assertNull($c->getValeur());
    }

    public function testCollectionnableCollectionsInitialesSontVides(): void
    {
        $c = new Collectionnable();

        $this->assertCount(0, $c->getDefi());
        $this->assertCount(0, $c->getEtudiants());
    }

    // --- Defis (ManyToMany owning, pas de relation inverse dans add) ---

    public function testCollectionnableAddCollectionDefiAjouteLElement(): void
    {
        $c = new Collectionnable();
        $defi = new Defi();

        $c->addDefi($defi);

        $this->assertCount(1, $c->getDefi());
        $this->assertTrue($c->getDefi()->contains($defi));
    }

    public function testCollectionnableAddCollectionDefiIgnoreLeDoublon(): void
    {
        $c = new Collectionnable();
        $defi = new Defi();

        $c->addDefi($defi);
        $c->addDefi($defi);

        $this->assertCount(1, $c->getDefi());
    }

    public function testCollectionnableRemoveCollectionDefiSupprimeLElement(): void
    {
        $c = new Collectionnable();
        $defi = new Defi();
        $c->addDefi($defi);

        $c->removeDefi($defi);

        $this->assertCount(0, $c->getDefi());
    }

    // --- Etudiants (ManyToMany inverse, synchronise via addCollectionnable) ---

    public function testCollectionnableAddCollectionEtudiantAjouteLElement(): void
    {
        $c = new Collectionnable();
        $etudiant = new Etudiant();

        $c->addEtudiant($etudiant);

        $this->assertCount(1, $c->getEtudiants());
        $this->assertTrue($c->getEtudiants()->contains($etudiant));
    }

    public function testCollectionnableAddCollectionEtudiantIgnoreLeDoublon(): void
    {
        $c = new Collectionnable();
        $etudiant = new Etudiant();

        $c->addEtudiant($etudiant);
        $c->addEtudiant($etudiant);

        $this->assertCount(1, $c->getEtudiants());
    }

    public function testCollectionnableAddCollectionEtudiantPositionneRelationInverse(): void
    {
        $c = new Collectionnable();
        $etudiant = new Etudiant();

        $c->addEtudiant($etudiant);

        $this->assertTrue($etudiant->getCollectionnable()->contains($c));
    }

    public function testCollectionnableRemoveCollectionEtudiantSupprimeLElement(): void
    {
        $c = new Collectionnable();
        $etudiant = new Etudiant();
        $c->addEtudiant($etudiant);

        $c->removeEtudiant($etudiant);

        $this->assertCount(0, $c->getEtudiants());
    }

    public function testCollectionnableRemoveCollectionEtudiantSupprimeCoteInverse(): void
    {
        $c = new Collectionnable();
        $etudiant = new Etudiant();
        $c->addEtudiant($etudiant);

        $c->removeEtudiant($etudiant);

        $this->assertFalse($etudiant->getCollectionnable()->contains($c));
    }
}
