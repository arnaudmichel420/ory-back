<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\ChoixQuizDefi;
use PHPUnit\Framework\TestCase;

class ChoixQuizDefiTest extends TestCase
{
    public function testChoixQuizDefiSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $choix = new ChoixQuizDefi();
        $result = $choix->setLibelle('Vrai');

        $this->assertSame($choix, $result);
        $this->assertSame('Vrai', $choix->getLibelle());
    }

    public function testChoixQuizDefiValeursInitialesNulles(): void
    {
        $choix = new ChoixQuizDefi();

        $this->assertNull($choix->getId());
        $this->assertNull($choix->getLibelle());
        $this->assertNull($choix->isEstCorrect());
        $this->assertNull($choix->getQuestionQuizz());
    }
}
