<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Etudiant;
use App\Entity\EtudiantDefi;
use PHPUnit\Framework\TestCase;

class EtudiantTest extends TestCase
{
    // --- Scalaires ---

    public function testEtudiantSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $etudiant = new Etudiant();
        $result = $etudiant->setNom('Dupont');

        $this->assertSame($etudiant, $result);
        $this->assertSame('Dupont', $etudiant->getNom());
    }

    public function testEtudiantValeursInitialesNulles(): void
    {
        $etudiant = new Etudiant();

        $this->assertNull($etudiant->getId());
        $this->assertNull($etudiant->getNom());
        $this->assertNull($etudiant->getPrenom());
    }

    // --- Collections initialisées ---

    public function testEtudiantCollectionsInitialesSontVides(): void
    {
        $etudiant = new Etudiant();

        $this->assertCount(0, $etudiant->getEtudiantDefis());
        $this->assertCount(0, $etudiant->getCollectionnable());
        $this->assertCount(0, $etudiant->getFavori());
    }

    // --- addEtudiantDefi : déduplication + relation inverse ---

    public function testEtudiantAddCollectionAjouteLElement(): void
    {
        $etudiant = new Etudiant();
        $defi = new EtudiantDefi();

        $etudiant->addEtudiantDefi($defi);

        $this->assertCount(1, $etudiant->getEtudiantDefis());
        $this->assertTrue($etudiant->getEtudiantDefis()->contains($defi));
    }

    public function testEtudiantAddCollectionIgnoreLeDoublon(): void
    {
        $etudiant = new Etudiant();
        $defi = new EtudiantDefi();

        $etudiant->addEtudiantDefi($defi);
        $etudiant->addEtudiantDefi($defi);

        $this->assertCount(1, $etudiant->getEtudiantDefis());
    }

    public function testEtudiantAddCollectionPositionneRelationInverse(): void
    {
        $etudiant = new Etudiant();
        $defi = new EtudiantDefi();

        $etudiant->addEtudiantDefi($defi);

        $this->assertSame($etudiant, $defi->getEtudiant());
    }

    // --- removeEtudiantDefi : mise à null de la FK ---

    public function testEtudiantRemoveCollectionSupprimeLElement(): void
    {
        $etudiant = new Etudiant();
        $defi = new EtudiantDefi();
        $etudiant->addEtudiantDefi($defi);

        $etudiant->removeEtudiantDefi($defi);

        $this->assertCount(0, $etudiant->getEtudiantDefis());
    }

    public function testEtudiantRemoveCollectionNullifyRelationInverse(): void
    {
        $etudiant = new Etudiant();
        $defi = new EtudiantDefi();
        $etudiant->addEtudiantDefi($defi);

        $etudiant->removeEtudiantDefi($defi);

        $this->assertNull($defi->getEtudiant());
    }

}
