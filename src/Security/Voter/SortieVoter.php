<?php

namespace App\Security\Voter;

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
    const ANNULER = "sortie_annuler";
    const MODIFIER = "sortie_modifier";
    const SUPP_BROUILLON = "sortie_annulerSortieNonEncorePubliee";

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
        return in_array($attribute, [self::AFFICHER, self::INSCRIRE, self::DESISTER,
                self::PUBLIER, self::ANNULER, self::MODIFIER, self::SUPP_BROUILLON])
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

        // il faut créer une fonction et l'appeller dans un nouveau 'case' avec les paramètres nécessaires
        switch ($attribute) {
            case self::AFFICHER:
                return $this->canAfficher($sortie);
            case self::INSCRIRE:
                return $this->canInscrire($sortie);
            case self::DESISTER:
                return $this->canDesister($sortie, $user);
            case self::PUBLIER:
                return $this->canPublier($sortie, $user);
            case self::ANNULER:
                return $this->canAnnuler($sortie, $user);
            case self::MODIFIER:
                return $this->canModifier($sortie, $user);
            case self::SUPP_BROUILLON:
                return $this->canSuppBrouillon($sortie, $user);
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
            && $sortie->getEtat()->getId() === 2 // id des sorties ouvertes
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
     * Vérifie si le participant est inscrit à la sortie et si la date de clôture n'est pas passée
     * @param Sortie $sortie
     * @param UserInterface $user
     * @return bool
     */
    private function canDesister(Sortie $sortie, UserInterface $user): bool
    {
        $dateDebut = $sortie->getDateHeureDebut();
        $datetime = new DateTime();
        $dateLimiteInscription = $sortie->getDateLimiteInscription();

        return $user->estInscrit($sortie) && $datetime<$dateLimiteInscription && $datetime<$dateDebut;
    }

    private function canPublier(Sortie $sortie, UserInterface $user): bool
    {
        $datetime = new DateTime();
        $clotureInscr = $sortie->getDateLimiteInscription();

        return $user->estOrganisateur($sortie) && $datetime<$clotureInscr
            && $sortie->getEtat()->getId()==1; // id des sorties crées
    }

    private function canAnnuler(Sortie $sortie, UserInterface $user)
    {
        $datetime = new DateTime();
        $dateLimiteInscription = $sortie->getDateLimiteInscription();
        $oldEtatId = $sortie->getEtat()->getId();

        return $oldEtatId==1 || $oldEtatId==2 // sortie crée ou ouverte
            && $this->security->isGranted('ROLE_ADMIN') || $user->estOrganisateur($sortie)
            && $datetime<$dateLimiteInscription;
    }

    private function canModifier(Sortie $sortie, UserInterface $user)
    {
        return $user->estOrganisateur($sortie)
            && $sortie->getEtat()->getId()==1;
    }

    private function canSuppBrouillon(Sortie $sortie, UserInterface $user)
    {
        return $user->estOrganisateur($sortie) && $sortie->getEtat()->getId()==1;
    }


}










