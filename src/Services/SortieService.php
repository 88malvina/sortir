<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class SortieService
{
    const CREE = 1;
    const OUVERTE = 2;
    const CLOTUREE = 3;
    const ENCOURS = 4;
    const PASSEE = 5;

    public function verifEtat(Sortie                 $sortie,
                              EtatRepository         $etatRepository,
                              EntityManagerInterface $manager
    ) {

        $date = new DateTime();
        $oldEtatId = $sortie->getEtat()->getId();
        $dateLimiteInsc = $sortie->getDateLimiteInscription();
        $dateDebutSortie = $sortie->getDateHeureDebut();
        $isComplete = $sortie->getParticipants()->count() == $sortie->getNbInscriptionMax();

        // clôturer les sorties
        if(($dateLimiteInsc < $date || $isComplete) && $oldEtatId == self::OUVERTE)
        {
            $newEtat = $etatRepository->find(self::CLOTUREE);
            $sortie->setEtat($newEtat);
            $manager->persist($sortie);
            $manager->flush();
        }



        // sorties passées
        if($dateDebutSortie<$date && $oldEtatId == self::CLOTUREE)
        {
            $newEtat = $etatRepository->find(self::PASSEE);
            $sortie->setEtat($newEtat);
            $manager->persist($sortie);
            $manager->flush();
        }


        /*
         *
        $minutes_to_add = $duree;
        $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
        $dateFinSortie = $time->format('Y-m-d H:i:s');
        var_dump($dateFinSortie);

        // activités en cours
        if($date>=$dateDebutSortie && $date<=$dateFinSortie && $oldEtat->getId() == self::CLOTUREE)
        {
            $newEtat = $etatRepository->find(self::ENCOURS);
            $sortie->setEtat($newEtat);
            $manager->persist($sortie);
            $manager->flush();
        }
        */











    }











}