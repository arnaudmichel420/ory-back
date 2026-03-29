<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MetierSecteur;
use PHPUnit\Framework\TestCase;

class MetierSecteurTest extends TestCase
{
    public function testMetierSecteurSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $ms = new MetierSecteur();
        $result = $ms->setPrincipal(true);

        $this->assertSame($ms, $result);
        $this->assertTrue($ms->isPrincipal());
    }

    public function testMetierSecteurValeursInitialesNulles(): void
    {
        $ms = new MetierSecteur();

        $this->assertNull($ms->getId());
        $this->assertNull($ms->getCodeOgrMetier());
        $this->assertNull($ms->getSecteur());
        $this->assertNull($ms->isPrincipal());
    }
}
