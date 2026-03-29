<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MetierContexteTravail;
use PHPUnit\Framework\TestCase;

class MetierContexteTravailTest extends TestCase
{
    public function testMetierContexteTravailSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $mct = new MetierContexteTravail();
        $result = $mct->setLibelleGroupe('Groupe A');

        $this->assertSame($mct, $result);
        $this->assertSame('Groupe A', $mct->getLibelleGroupe());
    }

    public function testMetierContexteTravailValeursInitialesNulles(): void
    {
        $mct = new MetierContexteTravail();

        $this->assertNull($mct->getId());
        $this->assertNull($mct->getCodeOgrMetier());
        $this->assertNull($mct->getCodeOgrContexte());
        $this->assertNull($mct->getLibelleGroupe());
    }
}
