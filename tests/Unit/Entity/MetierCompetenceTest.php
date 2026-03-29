<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MetierCompetence;
use App\Enum\MetierCompetenceTypeEnum;
use PHPUnit\Framework\TestCase;

class MetierCompetenceTest extends TestCase
{
    public function testMetierCompetenceSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $mc = new MetierCompetence();
        $result = $mc->setCoeurMetier(1);

        $this->assertSame($mc, $result);
        $this->assertSame(1, $mc->getCoeurMetier());
    }

    public function testMetierCompetenceValeursInitialesNulles(): void
    {
        $mc = new MetierCompetence();

        $this->assertNull($mc->getId());
        $this->assertNull($mc->getCodeOgrMetier());
        $this->assertNull($mc->getCodeOgrComp());
        $this->assertNull($mc->getType());
        $this->assertNull($mc->getLibelleEnjeu());
        $this->assertSame(0, $mc->getCoeurMetier());
    }

    public function testMetierCompetenceSetTypeStockeEnum(): void
    {
        $mc = new MetierCompetence();
        $mc->setType(MetierCompetenceTypeEnum::SAVOIR_FAIRE);

        $this->assertSame(MetierCompetenceTypeEnum::SAVOIR_FAIRE, $mc->getType());
    }
}
