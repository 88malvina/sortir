<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\MonProfilFormType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonProfilController extends AbstractController
{
    /**
     * @Route("/mon/profil/", name="monprofil_afficher")
     */
    public function afficher(): Response
    {
        $participant = new Participant();
        $profilForm = $this->createForm(MonProfilFormType::class, $participant);


        return $this->render('mon_profil/monProfil.html.twig', [
            "profilForm"=>$profilForm->createView()
        ]);
    }
}
