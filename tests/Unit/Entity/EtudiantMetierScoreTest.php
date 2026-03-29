<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\EtudiantMetierScore;
use PHPUnit\Framework\TestCase;

class EtudiantMetierScoreTest extends TestCase
{
    public function testEtudiantMetierScoreSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $ems = new EtudiantMetierScore();
        $result = $ems->setScoreTotal(42.5);

        $this->assertSame($ems, $result);
        $this->assertSame(42.5, $ems->getScoreTotal());
    }

    public function testEtudiantMetierScoreValeursInitialesNulles(): void
    {
        $ems = new EtudiantMetierScore();

        $this->assertNull($ems->getId());
        $this->assertNull($ems->getCodeOgrMetier());
        $this->assertNull($ems->getEtudiant());
        $this->assertNull($ems->getScoreTotal());
    }
}
