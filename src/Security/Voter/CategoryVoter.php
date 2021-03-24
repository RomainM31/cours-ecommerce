<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
	protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CAN_EDIT'])
            && $subject instanceof \App\Entity\Category;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // Si l'utilisateur est anonyme, je ne permet pas l'accès !
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (vérifie les conditions et retourne 'true' pour donner la permission) ...
        switch ($attribute) {
            case 'CAN_EDIT':
                return $subject->getOwner() === $user;
        }
        return false;
    }
}
