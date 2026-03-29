<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\EtudiantMetierInteraction;
use App\Enum\EtudiantMetierInteractionTypeEnum;
use PHPUnit\Framework\TestCase;

class EtudiantMetierInteractionTest extends TestCase
{
    public function testEtudiantMetierInteractionSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $emi = new EtudiantMetierInteraction();
        $result = $emi->setPoids(10);

        $this->assertSame($emi, $result);
        $this->assertSame(10, $emi->getPoids());
    }

    public function testEtudiantMetierInteractionValeursInitialesNulles(): void
    {
        $emi = new EtudiantMetierInteraction();

        $this->assertNull($emi->getId());
        $this->assertNull($emi->getCodeOgrMetier());
        $this->assertNull($emi->getEtudiant());
        $this->assertNull($emi->getType());
        $this->assertNull($emi->getPoids());
    }

    public function testEtudiantMetierInteractionSetTypeStockeEnum(): void
    {
        $emi = new EtudiantMetierInteraction();
        $emi->setType(EtudiantMetierInteractionTypeEnum::VUE);

        $this->assertSame(EtudiantMetierInteractionTypeEnum::VUE, $emi->getType());
    }
}
