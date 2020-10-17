<?php

namespace App\Security\Voter;

use App\Entity\Note;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteVoter extends Voter
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute  la permission demandée
     * @param mixed $subject     L'objet à évaluer
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['NOTE_EDIT', 'NOTE_DELETE'])
            && $subject instanceof Note;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        //Administrateurs: autorisés
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        //Auteur: autorisé
        /** @var Note $subject */
        if($subject->getAuthor() === $user){
            return true;
        }

        return false;
    }
}
