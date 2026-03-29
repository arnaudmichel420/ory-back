<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Appellation;
use PHPUnit\Framework\TestCase;

class AppellationTest extends TestCase
{
    public function testAppellationSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $appellation = new Appellation();
        $result = $appellation->setLibelle('Développeur');

        $this->assertSame($appellation, $result);
        $this->assertSame('Développeur', $appellation->getLibelle());
    }

    public function testAppellationValeursInitialesNulles(): void
    {
        $appellation = new Appellation();

        $this->assertNull($appellation->getCodeOgr());
        $this->assertNull($appellation->getLibelle());
        $this->assertNull($appellation->getLibelleCourt());
        $this->assertNull($appellation->isPeuUtiliser());
        $this->assertNull($appellation->getCodeOgrMetier());
    }
}
