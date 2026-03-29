<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ActionDefi;
use PHPUnit\Framework\TestCase;

class ActionDefiTest extends TestCase
{
    public function testActionDefiSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $action = new ActionDefi();
        $result = $action->setLibelle('Lire un article');

        $this->assertSame($action, $result);
        $this->assertSame('Lire un article', $action->getLibelle());
    }

    public function testActionDefiValeursInitialesNulles(): void
    {
        $action = new ActionDefi();

        $this->assertNull($action->getId());
        $this->assertNull($action->getDefi());
        $this->assertNull($action->getLibelle());
        $this->assertNull($action->getDescription());
        $this->assertNull($action->getNombreActions());
    }
}
