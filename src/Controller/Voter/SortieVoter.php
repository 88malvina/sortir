<?php

namespace App\Controller\Voter;

use App\Entity\Sortie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends \Symfony\Component\Security\Core\Authorization\Voter\Voter
{
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

        switch ($attribute) {
            case self::AFFICHER:
                return $this->canAfficher($sortie,$user);
        }

        return false;
    }

    private function canAfficher(Sortie $sortie, UserInterface $user)
    {
        $etat = $sortie->getEtat();

        if($etat->getId() == 6)
        {
            return false;
        } else
        {
            return true;
        }
    }


}










