<?php

declare(strict_types=1);

// phpstan-bootstrap.php
// Bootstrap pour PHPStan Doctrine sur Symfony 8

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

// Charge le .env pour les variables d'environnement (DATABASE_URL, etc.)
if (file_exists(__DIR__.'/.env')) {
    (new Dotenv())->bootEnv(__DIR__.'/.env');
}

// Boot le kernel Symfony en mode dev
$kernel = new Kernel('dev', true);
$kernel->boot();

/** @var EntityManagerInterface $em */
$em = $kernel->getContainer()->get('doctrine')->getManager();

// Retourne directement l'EntityManager pour PHPStan Doctrine
return $em;
