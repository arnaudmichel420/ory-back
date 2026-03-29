<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Trait;

use App\Entity\Trait\SoftDeleteableTrait;
use PHPUnit\Framework\TestCase;

class SoftDeleteableTraitTest extends TestCase
{
    private function makeEntity(): object
    {
        return new class {
            use SoftDeleteableTrait;
        };
    }

    public function testSoftDeleteableIsDeletedRetourneFalseParDefaut(): void
    {
        $entity = $this->makeEntity();

        $this->assertFalse($entity->isDeleted());
    }

    public function testSoftDeleteableIsDeletedRetourneTrueQuandSupprimeLe(): void
    {
        $entity = $this->makeEntity();
        $entity->setSupprimeLe(new \DateTimeImmutable());

        $this->assertTrue($entity->isDeleted());
    }

    public function testSoftDeleteableSetSupprimeleDateStockeDateTimeImmutable(): void
    {
        $entity = $this->makeEntity();
        $date = new \DateTimeImmutable('2024-06-01');

        $entity->setSupprimeLe($date);

        $this->assertSame($date, $entity->getSupprimeLe());
    }

    public function testSoftDeleteableIsDeletedRedevientFalseQuandSupprimeleMisANull(): void
    {
        $entity = $this->makeEntity();
        $entity->setSupprimeLe(new \DateTimeImmutable());

        $entity->setSupprimeLe(null);

        $this->assertFalse($entity->isDeleted());
    }
}
