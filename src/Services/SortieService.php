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
    const ANNULEE = 6;

    /**
     * @throws \Exception
     */
    public function verifEtat(Sortie                 $sortie,
                              EtatRepository         $etatRepository,
                              EntityManagerInterface $manager
    ) {
        $oldEtat = $sortie->getEtat();
        $dateLimiteInsc = $sortie->getDateLimiteInscription();
        $dateDebutSortie = $sortie->getDateHeureDebut();
        $duree = $sortie->getDuree();
        $date = new DateTime();
        $isComplete = $sortie->getParticipants()->count() == $sortie->getNbInscriptionMax();

        // clôturer les sorties
        if($dateLimiteInsc > $date || $isComplete && $oldEtat->getId() == self::OUVERTE)
        {
            $newEtat = $etatRepository->find(self::CLOTUREE);
            $sortie->setEtat($newEtat);
            $manager->persist($sortie);
            $manager->flush();
        }


        $minutes_to_add = $duree;

        $time = $date;
        $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
        $dateFinSortie = $time->format('Y-m-d H:i');

        // activités en cours
        if($date>=$dateDebutSortie && $date<=$dateFinSortie)
        {
            $newEtat = $etatRepository->find(self::ENCOURS);
            $sortie->setEtat($newEtat);
            $manager->persist($sortie);
            $manager->flush();
        }






    }











}