<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\PoleEmploi;

use App\Entity\Appellation;
use App\Entity\CentreInteret;
use App\Entity\Competence;
use App\Entity\ContexteTravail;
use App\Entity\Domaine;
use App\Entity\Metier;
use App\Entity\Secteur;
use App\Entity\SousDomaine;
use App\Enum\MetierCompetenceTypeEnum;
use App\Service\PoleEmploi\ImportContext;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class PoleEmploiServiceTestCase extends TestCase
{
    /**
     * @param array<int, object>|null $persisted
     * @param array<int, object>|null $removed
     */
    protected function createEntityManagerMock(
        ?array &$persisted = null,
        ?array &$removed = null,
        int $persistCount = 0,
        int $removeCount = 0,
        int $flushCount = 0,
    ): EntityManagerInterface&MockObject {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $persisted ??= [];
        $removed ??= [];

        if ($persistCount > 0) {
            $entityManager
                ->expects($this->exactly($persistCount))
                ->method('persist')
                ->willReturnCallback(static function (object $entity) use (&$persisted): void {
                    $persisted[] = $entity;
                });
        } else {
            $entityManager
                ->expects($this->never())
                ->method('persist');
        }

        if ($removeCount > 0) {
            $entityManager
                ->expects($this->exactly($removeCount))
                ->method('remove')
                ->willReturnCallback(static function (object $entity) use (&$removed): void {
                    $removed[] = $entity;
                });
        } else {
            $entityManager
                ->expects($this->never())
                ->method('remove');
        }

        if ($flushCount > 0) {
            $entityManager
                ->expects($this->exactly($flushCount))
                ->method('flush');
        } else {
            $entityManager
                ->expects($this->never())
                ->method('flush');
        }

        return $entityManager;
    }

    protected function createMetier(string $codeOgr, string $codeRome): Metier
    {
        return (new Metier())
            ->setCodeOgr($codeOgr)
            ->setCodeRome($codeRome)
            ->setLibelle('Metier '.$codeRome);
    }

    protected function createSousDomaine(string $code, ?Domaine $domaine = null): SousDomaine
    {
        $sousDomaine = (new SousDomaine())
            ->setCode($code)
            ->setLibelle('Sous-domaine '.$code);

        if (null !== $domaine) {
            $sousDomaine->setDomaine($domaine);
        }

        return $sousDomaine;
    }

    protected function createDomaine(string $code): Domaine
    {
        return (new Domaine())
            ->setCode($code)
            ->setLibelle('Domaine '.$code);
    }

    protected function createCentreInteret(string $libelle): CentreInteret
    {
        return (new CentreInteret())
            ->setLibelle($libelle)
            ->setDefinition('Definition '.$libelle);
    }

    protected function createSecteur(string $code): Secteur
    {
        return (new Secteur())
            ->setCode($code)
            ->setLibelle('Secteur '.$code)
            ->setDefinition('Definition '.$code);
    }

    protected function createContexteTravail(string $code): ContexteTravail
    {
        return (new ContexteTravail())
            ->setCodeOgr($code)
            ->setLibelle('Contexte '.$code);
    }

    protected function createCompetence(string $code, MetierCompetenceTypeEnum $type): Competence
    {
        return (new Competence())
            ->setCodeOgr($code)
            ->setLibelle('Competence '.$code)
            ->setType($type);
    }

    protected function createAppellation(string $codeOgr, Metier $metier): Appellation
    {
        return (new Appellation())
            ->setCodeOgr($codeOgr)
            ->setLibelle('Appellation '.$codeOgr)
            ->setCodeOgrMetier($metier);
    }

    /**
     * @template TValue
     *
     * @param array<string, TValue> $elements
     *
     * @return TValue
     */
    protected function getElementParCle(array $elements, string $cle)
    {
        if (!\array_key_exists($cle, $elements)) {
            self::fail(\sprintf('La cle "%s" est absente du tableau teste.', $cle));
        }

        return $elements[$cle];
    }

    protected function ajouterCompetenceAuContexte(ImportContext $contexte, string $code, Competence $competence): void
    {
        $contexte->competencesParCode[$code] = $competence;
    }

    protected function ajouterContexteTravailAuContexte(ImportContext $contexte, string $code, ContexteTravail $contexteTravail): void
    {
        $contexte->contextesTravailParCode[$code] = $contexteTravail;
    }

    protected function ajouterSecteurAuContexte(ImportContext $contexte, string $code, Secteur $secteur): void
    {
        $contexte->secteursParCode[$code] = $secteur;
    }
}
