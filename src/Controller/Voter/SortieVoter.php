<?php

namespace App\Controller\Voter;

use App\Entity\Sortie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends \Symfony\Component\Security\Core\Authorization\Voter\Voter
{
    // constantes = une pour chaque méthode
    const AFFICHER = "sortie_afficher";

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        // ajouter la constante au array (self::NOMCONST)
        return in_array($attribute, [self::AFFICHER])
            && $subject instanceof Sortie;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof UserInterface)
        {
            return false;
        }

        $sortie = $subject;

        // il faut créer une fonction et l'appeller dans un case
        switch ($attribute) {
            case self::AFFICHER:
                return $this->canAfficher($sortie,$user);
        }

        return false;
    }

    /**
     * Vérifie si la sortie peut être affichée
     */
    private function canAfficher(Sortie $sortie, UserInterface $user)
    {
        $verifEtat = false;

        // vérifie si la sortie est annulée (etat id 6)
        $etat = $sortie->getEtat();
        if($etat->getId() == 6)
        {
            $verifEtat = false;
        } else
        {
            $verifEtat = true;
        }


        // ajouter des autres verifications ici


        return ($verifEtat);
    }


}










