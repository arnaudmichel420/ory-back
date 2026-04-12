<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\MetierAttractivite;
use App\Enum\MetierAttractiviteCodeEnum;
use PHPUnit\Framework\TestCase;

class MetierAttractiviteTest extends TestCase
{
    public function testMetierAttractiviteSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $ma = new MetierAttractivite();
        $result = $ma->setCodeAttractivite(MetierAttractiviteCodeEnum::INT_EMB);

        $this->assertSame($ma, $result);
        $this->assertSame(MetierAttractiviteCodeEnum::INT_EMB, $ma->getCodeAttractivite());
    }

    public function testMetierAttractiviteValeursInitialesNulles(): void
    {
        $ma = new MetierAttractivite();

        $this->assertNull($ma->getId());
        $this->assertNull($ma->getCodeOgrMetier());
        $this->assertNull($ma->getCodeAttractivite());
        $this->assertNull($ma->getTerritoire());
        $this->assertNull($ma->getValeur());
    }
}
