<?php

namespace App\Security\Voter;

use App\Entity\Sortie;
use DateTime;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends Voter
{
    // constantes METHODE = "route"
    const AFFICHER = "sortie_afficher";
    const INSCRIRE = "sortie_inscrire";

    private $security;

    // pour les flash si necessaire
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(Security $security, ContainerInterface $container)
    {

        $this->security = $security;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        // ajouter la constante au array (self::NOMCONST)
        return in_array($attribute, [self::AFFICHER, self::INSCRIRE])
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

        // il faut créer une fonction et l'appeller dans un nouveau 'case'
        switch ($attribute) {
            case self::AFFICHER:
                return $this->canAfficher($sortie);
            case self::INSCRIRE:
                return $this->canInscrire($sortie);
        }

        return false;
    }

    /**
     * Vérifie si la sortie peut être affichée
     * @param Sortie $sortie
     * @return bool
     */
    private function canAfficher(Sortie $sortie): bool
    {
        // add flash
        // $this->container->get('session')->getFlashBag()->add('success','blabla');

        // vérifie si la sortie est annulée (etat id 6) ou créée ()
        $etat = $sortie->getEtat();
        if ($etat->getId() == 6)
        {

            return false;
        }
        else
        {
            return true;
        }
    }

    /*----- en passant 3 paramètres, j'ai eu 2 conflits différents :
    /------ avec la fonction is_granted($role, $object = null, string $field = null) dans twig
    //----- et denyAccessUnlessGranted($attribute, $subject = null, string $message = 'Access Denied.') dans le controller
    */
    /**
     * @param Sortie $sortie
     * @return bool
     */
    private function canInscrire(Sortie $sortie): bool
    {
        $datetime = new DateTime();

        if (($this->security->isGranted('ROLE_USER') || $this->security->isGranted('ROLE_ADMIN'))
            && $sortie->getEtat()->getId() === 2
            && $sortie->getDateLimiteInscription() > $datetime
            && $sortie->getNbInscriptionMax() > $sortie->getParticipants()->count())
        {
            return true;
        }
        else
        {
            return false;
        }
    }


}










