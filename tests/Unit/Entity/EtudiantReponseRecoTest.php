<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Etudiant;
use App\Entity\EtudiantReponseReco;
use PHPUnit\Framework\TestCase;

class EtudiantReponseRecoTest extends TestCase
{
    public function testEtudiantReponseRecoSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $err = new EtudiantReponseReco();
        $etudiant = new Etudiant();
        $result = $err->setEtudiant($etudiant);

        $this->assertSame($err, $result);
        $this->assertSame($etudiant, $err->getEtudiant());
    }

    public function testEtudiantReponseRecoValeursInitialesNulles(): void
    {
        $err = new EtudiantReponseReco();

        $this->assertNull($err->getId());
        $this->assertNull($err->getEtudiant());
        $this->assertNull($err->getChoix());
    }
}
