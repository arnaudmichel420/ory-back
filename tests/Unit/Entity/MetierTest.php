<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Appellation;
use App\Entity\Etudiant;
use App\Entity\EtudiantMetierInteraction;
use App\Entity\EtudiantMetierScore;
use App\Entity\Metier;
use App\Entity\MetierAttractivite;
use App\Entity\MetierCentreInteret;
use App\Entity\MetierCompetence;
use App\Entity\MetierContexteTravail;
use App\Entity\MetierSecteur;
use App\Entity\Mobilite;
use PHPUnit\Framework\TestCase;

class MetierTest extends TestCase
{
    public function testMetierSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $metier = new Metier();
        $result = $metier->setLibelle('Développeur');

        $this->assertSame($metier, $result);
        $this->assertSame('Développeur', $metier->getLibelle());
    }

    public function testMetierValeursInitialesNulles(): void
    {
        $metier = new Metier();

        $this->assertNull($metier->getCodeOgr());
        $this->assertNull($metier->getCodeRome());
        $this->assertNull($metier->getLibelle());
        $this->assertNull($metier->getDefinition());
        $this->assertNull($metier->getAccesMetier());
        $this->assertNull($metier->isTransitionEco());
        $this->assertNull($metier->isTransitionNum());
        $this->assertNull($metier->isEmploiReglemente());
        $this->assertNull($metier->isEmploiCadre());
        $this->assertNull($metier->getSousDomaine());
    }

    public function testMetierCollectionsInitialesSontVides(): void
    {
        $metier = new Metier();

        $this->assertCount(0, $metier->getAppellations());
        $this->assertCount(0, $metier->getEtudiants());
        $this->assertCount(0, $metier->getMetierCompetences());
        $this->assertCount(0, $metier->getMetierCentreInterets());
        $this->assertCount(0, $metier->getMetierSecteurs());
        $this->assertCount(0, $metier->getMetierContexteTravails());
        $this->assertCount(0, $metier->getMobilites());
        $this->assertCount(0, $metier->getEtudiantMetierInteractions());
        $this->assertCount(0, $metier->getEtudiantMetierScores());
        $this->assertCount(0, $metier->getMetierAttractivites());
    }

    // --- Appellations ---

    public function testMetierAddCollectionAppellationAjouteLElement(): void
    {
        $metier = new Metier();
        $appellation = new Appellation();

        $metier->addAppellation($appellation);

        $this->assertCount(1, $metier->getAppellations());
        $this->assertTrue($metier->getAppellations()->contains($appellation));
    }

    public function testMetierAddCollectionAppellationIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $appellation = new Appellation();

        $metier->addAppellation($appellation);
        $metier->addAppellation($appellation);

        $this->assertCount(1, $metier->getAppellations());
    }

    public function testMetierAddCollectionAppellationPositionneRelationInverse(): void
    {
        $metier = new Metier();
        $appellation = new Appellation();

        $metier->addAppellation($appellation);

        $this->assertSame($metier, $appellation->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionAppellationSupprimeLElement(): void
    {
        $metier = new Metier();
        $appellation = new Appellation();
        $metier->addAppellation($appellation);

        $metier->removeAppellation($appellation);

        $this->assertCount(0, $metier->getAppellations());
    }

    public function testMetierRemoveCollectionAppellationNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $appellation = new Appellation();
        $metier->addAppellation($appellation);

        $metier->removeAppellation($appellation);

        $this->assertNull($appellation->getCodeOgrMetier());
    }

    // --- Etudiants (ManyToMany owning, pas de relation inverse dans add) ---

    public function testMetierAddCollectionEtudiantAjouteLElement(): void
    {
        $metier = new Metier();
        $etudiant = new Etudiant();

        $metier->addEtudiant($etudiant);

        $this->assertCount(1, $metier->getEtudiants());
        $this->assertTrue($metier->getEtudiants()->contains($etudiant));
    }

    public function testMetierAddCollectionEtudiantIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $etudiant = new Etudiant();

        $metier->addEtudiant($etudiant);
        $metier->addEtudiant($etudiant);

        $this->assertCount(1, $metier->getEtudiants());
    }

    public function testMetierRemoveCollectionEtudiantSupprimeLElement(): void
    {
        $metier = new Metier();
        $etudiant = new Etudiant();
        $metier->addEtudiant($etudiant);

        $metier->removeEtudiant($etudiant);

        $this->assertCount(0, $metier->getEtudiants());
    }

    // --- MetierCompetences ---

    public function testMetierAddCollectionMetierCompetenceAjouteLElement(): void
    {
        $metier = new Metier();
        $mc = new MetierCompetence();

        $metier->addMetierCompetence($mc);

        $this->assertCount(1, $metier->getMetierCompetences());
        $this->assertTrue($metier->getMetierCompetences()->contains($mc));
    }

    public function testMetierAddCollectionMetierCompetenceIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $mc = new MetierCompetence();

        $metier->addMetierCompetence($mc);
        $metier->addMetierCompetence($mc);

        $this->assertCount(1, $metier->getMetierCompetences());
    }

    public function testMetierAddCollectionMetierCompetencePositionneRelationInverse(): void
    {
        $metier = new Metier();
        $mc = new MetierCompetence();

        $metier->addMetierCompetence($mc);

        $this->assertSame($metier, $mc->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionMetierCompetenceSupprimeLElement(): void
    {
        $metier = new Metier();
        $mc = new MetierCompetence();
        $metier->addMetierCompetence($mc);

        $metier->removeMetierCompetence($mc);

        $this->assertCount(0, $metier->getMetierCompetences());
    }

    public function testMetierRemoveCollectionMetierCompetenceNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $mc = new MetierCompetence();
        $metier->addMetierCompetence($mc);

        $metier->removeMetierCompetence($mc);

        $this->assertNull($mc->getCodeOgrMetier());
    }

    // --- MetierCentreInterets ---

    public function testMetierAddCollectionMetierCentreInteretAjouteLElement(): void
    {
        $metier = new Metier();
        $mci = new MetierCentreInteret();

        $metier->addMetierCentreInteret($mci);

        $this->assertCount(1, $metier->getMetierCentreInterets());
        $this->assertTrue($metier->getMetierCentreInterets()->contains($mci));
    }

    public function testMetierAddCollectionMetierCentreInteretIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $mci = new MetierCentreInteret();

        $metier->addMetierCentreInteret($mci);
        $metier->addMetierCentreInteret($mci);

        $this->assertCount(1, $metier->getMetierCentreInterets());
    }

    public function testMetierAddCollectionMetierCentreInteretPositionneRelationInverse(): void
    {
        $metier = new Metier();
        $mci = new MetierCentreInteret();

        $metier->addMetierCentreInteret($mci);

        $this->assertSame($metier, $mci->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionMetierCentreInteretSupprimeLElement(): void
    {
        $metier = new Metier();
        $mci = new MetierCentreInteret();
        $metier->addMetierCentreInteret($mci);

        $metier->removeMetierCentreInteret($mci);

        $this->assertCount(0, $metier->getMetierCentreInterets());
    }

    public function testMetierRemoveCollectionMetierCentreInteretNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $mci = new MetierCentreInteret();
        $metier->addMetierCentreInteret($mci);

        $metier->removeMetierCentreInteret($mci);

        $this->assertNull($mci->getCodeOgrMetier());
    }

    // --- MetierSecteurs ---

    public function testMetierAddCollectionMetierSecteurAjouteLElement(): void
    {
        $metier = new Metier();
        $ms = new MetierSecteur();

        $metier->addMetierSecteur($ms);

        $this->assertCount(1, $metier->getMetierSecteurs());
        $this->assertTrue($metier->getMetierSecteurs()->contains($ms));
    }

    public function testMetierAddCollectionMetierSecteurIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $ms = new MetierSecteur();

        $metier->addMetierSecteur($ms);
        $metier->addMetierSecteur($ms);

        $this->assertCount(1, $metier->getMetierSecteurs());
    }

    public function testMetierAddCollectionMetierSecteurPositionneRelationInverse(): void
    {
        $metier = new Metier();
        $ms = new MetierSecteur();

        $metier->addMetierSecteur($ms);

        $this->assertSame($metier, $ms->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionMetierSecteurSupprimeLElement(): void
    {
        $metier = new Metier();
        $ms = new MetierSecteur();
        $metier->addMetierSecteur($ms);

        $metier->removeMetierSecteur($ms);

        $this->assertCount(0, $metier->getMetierSecteurs());
    }

    public function testMetierRemoveCollectionMetierSecteurNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $ms = new MetierSecteur();
        $metier->addMetierSecteur($ms);

        $metier->removeMetierSecteur($ms);

        $this->assertNull($ms->getCodeOgrMetier());
    }

    // --- MetierContexteTravails ---

    public function testMetierAddCollectionMetierContexteTravailAjouteLElement(): void
    {
        $metier = new Metier();
        $mct = new MetierContexteTravail();

        $metier->addMetierContexteTravail($mct);

        $this->assertCount(1, $metier->getMetierContexteTravails());
        $this->assertTrue($metier->getMetierContexteTravails()->contains($mct));
    }

    public function testMetierAddCollectionMetierContexteTravailIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $mct = new MetierContexteTravail();

        $metier->addMetierContexteTravail($mct);
        $metier->addMetierContexteTravail($mct);

        $this->assertCount(1, $metier->getMetierContexteTravails());
    }

    public function testMetierAddCollectionMetierContexteTravailPositionneRelationInverse(): void
    {
        $metier = new Metier();
        $mct = new MetierContexteTravail();

        $metier->addMetierContexteTravail($mct);

        $this->assertSame($metier, $mct->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionMetierContexteTravailSupprimeLElement(): void
    {
        $metier = new Metier();
        $mct = new MetierContexteTravail();
        $metier->addMetierContexteTravail($mct);

        $metier->removeMetierContexteTravail($mct);

        $this->assertCount(0, $metier->getMetierContexteTravails());
    }

    public function testMetierRemoveCollectionMetierContexteTravailNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $mct = new MetierContexteTravail();
        $metier->addMetierContexteTravail($mct);

        $metier->removeMetierContexteTravail($mct);

        $this->assertNull($mct->getCodeOgrMetier());
    }

    // --- Mobilites ---

    public function testMetierAddCollectionMobiliteAjouteLElement(): void
    {
        $metier = new Metier();
        $mobilite = new Mobilite();

        $metier->addMobilite($mobilite);

        $this->assertCount(1, $metier->getMobilites());
        $this->assertTrue($metier->getMobilites()->contains($mobilite));
    }

    public function testMetierAddCollectionMobiliteIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $mobilite = new Mobilite();

        $metier->addMobilite($mobilite);
        $metier->addMobilite($mobilite);

        $this->assertCount(1, $metier->getMobilites());
    }

    public function testMetierAddCollectionMobilitePositionneRelationInverse(): void
    {
        $metier = new Metier();
        $mobilite = new Mobilite();

        $metier->addMobilite($mobilite);

        $this->assertSame($metier, $mobilite->getCodeOgrMetierSource());
    }

    public function testMetierRemoveCollectionMobiliteSupprimeLElement(): void
    {
        $metier = new Metier();
        $mobilite = new Mobilite();
        $metier->addMobilite($mobilite);

        $metier->removeMobilite($mobilite);

        $this->assertCount(0, $metier->getMobilites());
    }

    public function testMetierRemoveCollectionMobiliteNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $mobilite = new Mobilite();
        $metier->addMobilite($mobilite);

        $metier->removeMobilite($mobilite);

        $this->assertNull($mobilite->getCodeOgrMetierSource());
    }

    // --- EtudiantMetierInteractions ---

    public function testMetierAddCollectionEtudiantMetierInteractionAjouteLElement(): void
    {
        $metier = new Metier();
        $emi = new EtudiantMetierInteraction();

        $metier->addEtudiantMetierInteraction($emi);

        $this->assertCount(1, $metier->getEtudiantMetierInteractions());
        $this->assertTrue($metier->getEtudiantMetierInteractions()->contains($emi));
    }

    public function testMetierAddCollectionEtudiantMetierInteractionIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $emi = new EtudiantMetierInteraction();

        $metier->addEtudiantMetierInteraction($emi);
        $metier->addEtudiantMetierInteraction($emi);

        $this->assertCount(1, $metier->getEtudiantMetierInteractions());
    }

    public function testMetierAddCollectionEtudiantMetierInteractionPositionneRelationInverse(): void
    {
        $metier = new Metier();
        $emi = new EtudiantMetierInteraction();

        $metier->addEtudiantMetierInteraction($emi);

        $this->assertSame($metier, $emi->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionEtudiantMetierInteractionSupprimeLElement(): void
    {
        $metier = new Metier();
        $emi = new EtudiantMetierInteraction();
        $metier->addEtudiantMetierInteraction($emi);

        $metier->removeEtudiantMetierInteraction($emi);

        $this->assertCount(0, $metier->getEtudiantMetierInteractions());
    }

    public function testMetierRemoveCollectionEtudiantMetierInteractionNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $emi = new EtudiantMetierInteraction();
        $metier->addEtudiantMetierInteraction($emi);

        $metier->removeEtudiantMetierInteraction($emi);

        $this->assertNull($emi->getCodeOgrMetier());
    }

    // --- EtudiantMetierScores ---

    public function testMetierAddCollectionEtudiantMetierScoreAjouteLElement(): void
    {
        $metier = new Metier();
        $ems = new EtudiantMetierScore();

        $metier->addEtudiantMetierScore($ems);

        $this->assertCount(1, $metier->getEtudiantMetierScores());
        $this->assertTrue($metier->getEtudiantMetierScores()->contains($ems));
    }

    public function testMetierAddCollectionEtudiantMetierScoreIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $ems = new EtudiantMetierScore();

        $metier->addEtudiantMetierScore($ems);
        $metier->addEtudiantMetierScore($ems);

        $this->assertCount(1, $metier->getEtudiantMetierScores());
    }

    public function testMetierAddCollectionEtudiantMetierScorePositionneRelationInverse(): void
    {
        $metier = new Metier();
        $ems = new EtudiantMetierScore();

        $metier->addEtudiantMetierScore($ems);

        $this->assertSame($metier, $ems->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionEtudiantMetierScoreSupprimeLElement(): void
    {
        $metier = new Metier();
        $ems = new EtudiantMetierScore();
        $metier->addEtudiantMetierScore($ems);

        $metier->removeEtudiantMetierScore($ems);

        $this->assertCount(0, $metier->getEtudiantMetierScores());
    }

    public function testMetierRemoveCollectionEtudiantMetierScoreNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $ems = new EtudiantMetierScore();
        $metier->addEtudiantMetierScore($ems);

        $metier->removeEtudiantMetierScore($ems);

        $this->assertNull($ems->getCodeOgrMetier());
    }

    // --- MetierAttractivites ---

    public function testMetierAddCollectionMetierAttractiviteAjouteLElement(): void
    {
        $metier = new Metier();
        $ma = new MetierAttractivite();

        $metier->addMetierAttractivite($ma);

        $this->assertCount(1, $metier->getMetierAttractivites());
        $this->assertTrue($metier->getMetierAttractivites()->contains($ma));
    }

    public function testMetierAddCollectionMetierAttractiviteIgnoreLeDoublon(): void
    {
        $metier = new Metier();
        $ma = new MetierAttractivite();

        $metier->addMetierAttractivite($ma);
        $metier->addMetierAttractivite($ma);

        $this->assertCount(1, $metier->getMetierAttractivites());
    }

    public function testMetierAddCollectionMetierAttractivitePositionneRelationInverse(): void
    {
        $metier = new Metier();
        $ma = new MetierAttractivite();

        $metier->addMetierAttractivite($ma);

        $this->assertSame($metier, $ma->getCodeOgrMetier());
    }

    public function testMetierRemoveCollectionMetierAttractiviteSupprimeLElement(): void
    {
        $metier = new Metier();
        $ma = new MetierAttractivite();
        $metier->addMetierAttractivite($ma);

        $metier->removeMetierAttractivite($ma);

        $this->assertCount(0, $metier->getMetierAttractivites());
    }

    public function testMetierRemoveCollectionMetierAttractiviteNullifyRelationInverse(): void
    {
        $metier = new Metier();
        $ma = new MetierAttractivite();
        $metier->addMetierAttractivite($ma);

        $metier->removeMetierAttractivite($ma);

        $this->assertNull($ma->getCodeOgrMetier());
    }
}
