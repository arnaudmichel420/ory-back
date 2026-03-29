<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Mobilite;
use PHPUnit\Framework\TestCase;

class MobiliteTest extends TestCase
{
    public function testMobiliteSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $mobilite = new Mobilite();
        $result = $mobilite->setCodeOgrMetierCible('M1234');

        $this->assertSame($mobilite, $result);
        $this->assertSame('M1234', $mobilite->getCodeOgrMetierCible());
    }

    public function testMobiliteValeursInitialesNulles(): void
    {
        $mobilite = new Mobilite();

        $this->assertNull($mobilite->getId());
        $this->assertNull($mobilite->getCodeOgrMetierSource());
        $this->assertNull($mobilite->getOrdreMobilite());
        $this->assertNull($mobilite->getCodeOgrMetierCible());
    }
}
