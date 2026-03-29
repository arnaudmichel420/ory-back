<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\EtudiantDefi;
use App\Enum\EtudiantDefiStatutEnum;
use PHPUnit\Framework\TestCase;

class EtudiantDefiTest extends TestCase
{
    public function testEtudiantDefiSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $ed = new EtudiantDefi();
        $result = $ed->setProgression(50);

        $this->assertSame($ed, $result);
        $this->assertSame(50, $ed->getProgression());
    }

    public function testEtudiantDefiValeursInitialesNulles(): void
    {
        $ed = new EtudiantDefi();

        $this->assertNull($ed->getId());
        $this->assertNull($ed->getEtudiant());
        $this->assertNull($ed->getDefi());
        $this->assertNull($ed->getStatut());
        $this->assertNull($ed->getProgression());
        $this->assertNull($ed->getCompleteLe());
    }

    public function testEtudiantDefiSetStatutStockeEnum(): void
    {
        $ed = new EtudiantDefi();
        $ed->setStatut(EtudiantDefiStatutEnum::EN_COURS);

        $this->assertSame(EtudiantDefiStatutEnum::EN_COURS, $ed->getStatut());
    }

    public function testEtudiantDefiSetCompleteLeStockeDateTimeImmutable(): void
    {
        $ed = new EtudiantDefi();
        $date = new \DateTimeImmutable('2024-01-15');

        $ed->setCompleteLe($date);

        $this->assertSame($date, $ed->getCompleteLe());
    }
}
