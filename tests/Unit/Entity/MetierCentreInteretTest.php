<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MetierCentreInteret;
use PHPUnit\Framework\TestCase;

class MetierCentreInteretTest extends TestCase
{
    public function testMetierCentreInteretSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $mci = new MetierCentreInteret();
        $result = $mci->setPrincipal(true);

        $this->assertSame($mci, $result);
        $this->assertTrue($mci->isPrincipal());
    }

    public function testMetierCentreInteretValeursInitialesNulles(): void
    {
        $mci = new MetierCentreInteret();

        $this->assertNull($mci->getId());
        $this->assertNull($mci->getCentreInteret());
        $this->assertNull($mci->getCodeOgrMetier());
        $this->assertNull($mci->isPrincipal());
    }
}
