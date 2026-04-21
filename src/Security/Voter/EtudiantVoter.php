<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Etudiant;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Etudiant|Utilisateur>
 */
final class EtudiantVoter extends Voter
{
    public const VIEW = 'ETUDIANT_VIEW';
    public const EDIT = 'ETUDIANT_EDIT';
    public const DELETE = 'ETUDIANT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && ($subject instanceof Etudiant || $subject instanceof Utilisateur);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Utilisateur) {
            return false;
        }

        if ($subject instanceof Utilisateur) {
            return $subject === $user
                || (
                    null !== $subject->getId()
                    && $subject->getId() === $user->getId()
                );
        }

        return $subject->getUtilisateur() === $user
            || (
                null !== $subject->getUtilisateur()?->getId()
                && $subject->getUtilisateur()->getId() === $user->getId()
            );
    }
}
