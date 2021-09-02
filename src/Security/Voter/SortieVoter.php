<?php

namespace App\Security\Voter;

use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends Voter
{
    // constantes METHODE = "route"
    const AFFICHER = "sortie_afficher";
    const INSCRIRE = "sortie_inscrire";
    const DESISTER = "sortie_desister";
    const PUBLIER = "sortie_publier";

    private $security;

    // pour les flash si necessaire
    ///**
    // * @var ContainerInterface
    // */
    //private $container;

    public function __construct(Security $security)
    {
        $this->security = $security;
        // $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        // ajouter la constante au array (self::NOMCONST)
        return in_array($attribute, [self::AFFICHER, self::INSCRIRE, self::DESISTER])
            && $subject instanceof Sortie;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
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
            case self::DESISTER:
                return $this->canDesister($sortie, $user);
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


    /**
     * Vérifie s'il est possible de s'inscrire à la sortie
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

    /**
     * Vérifie s'il est possible de se désister de la sortie
     * @param Sortie $sortie
     * @param Participant $participant
     * @return bool
     */
    private function canDesister(Sortie $sortie, Participant $participant): bool
    {
        $dateDebut = $sortie->getDateHeureDebut();
        $datetime = new DateTime();
        $clotureInscr = $sortie->getDateLimiteInscription();

        return $participant->estInscrit($sortie) && $datetime < $clotureInscr && $datetime < $dateDebut;
    }


}










