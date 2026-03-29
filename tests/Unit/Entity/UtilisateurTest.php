<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Etudiant;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    public function testUtilisateurSetterStockeValeurEtRetourneFluentInterface(): void
    {
        $utilisateur = new Utilisateur();
        $result = $utilisateur->setEmail('test@example.com');

        $this->assertSame($utilisateur, $result);
        $this->assertSame('test@example.com', $utilisateur->getEmail());
    }

    public function testUtilisateurValeursInitialesNulles(): void
    {
        $utilisateur = new Utilisateur();

        $this->assertNull($utilisateur->getId());
        $this->assertNull($utilisateur->getEmail());
        $this->assertNull($utilisateur->getPassword());
        $this->assertNull($utilisateur->getEtudiant());
    }

    // --- getRoles : logique métier ---

    public function testUtilisateurGetRolesRetourneToujrsRoleUser(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRoles([]);

        $this->assertContains('ROLE_USER', $utilisateur->getRoles());
    }

    public function testUtilisateurGetRolesInclutRolesAssignes(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRoles(['ROLE_ADMIN']);

        $roles = $utilisateur->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testUtilisateurGetRolesDeduplique(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRoles(['ROLE_USER']);

        $roles = $utilisateur->getRoles();

        $this->assertCount(1, $roles);
        $this->assertSame(['ROLE_USER'], array_values($roles));
    }

    // --- getUserIdentifier ---

    public function testUtilisateurGetUserIdentifierRetourneEmail(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('user@example.com');

        $this->assertSame('user@example.com', $utilisateur->getUserIdentifier());
    }

    // --- setEtudiant : logique de synchronisation bidirectionnelle ---

    public function testUtilisateurSetEtudiantPositionneRelationInverse(): void
    {
        $utilisateur = new Utilisateur();
        $etudiant = new Etudiant();

        $utilisateur->setEtudiant($etudiant);

        $this->assertSame($utilisateur, $etudiant->getUtilisateur());
    }

    public function testUtilisateurSetEtudiantNeReappliquesPasRelationSiDejaPositionnee(): void
    {
        $utilisateur = new Utilisateur();
        $etudiant = new Etudiant();
        $etudiant->setUtilisateur($utilisateur);

        // Ne doit pas boucler infiniment
        $utilisateur->setEtudiant($etudiant);

        $this->assertSame($etudiant, $utilisateur->getEtudiant());
    }

    // --- __serialize : hachage du password ---

    public function testUtilisateurSerializeHacheLePassword(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setPassword('monMotDePasse');

        $data = $utilisateur->__serialize();

        $passwordKey = "\0".Utilisateur::class."\0password";
        $this->assertArrayHasKey($passwordKey, $data);
        $this->assertSame(hash('crc32c', 'monMotDePasse'), $data[$passwordKey]);
        $this->assertNotSame('monMotDePasse', $data[$passwordKey]);
    }
}
