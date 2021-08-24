<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\MonProfilFormType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonProfilController extends AbstractController
{
    /**
     * @Route("/mon/profil/", name="monprofil_afficher")
     */
    public function afficher(Request $request): Response
    {
        $participant = new Participant();
        $profilForm = $this->createForm(MonProfilFormType::class, $participant);

        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted()){
            return $this->redirectToRoute('main');
        }

        return $this->render('mon_profil/monProfil.html.twig', [
            "profilForm"=>$profilForm->createView()
        ]);
    }
}
