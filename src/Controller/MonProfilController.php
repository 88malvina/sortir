<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\MonProfilFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MonProfilController extends AbstractController
{
    /**
     * @Route("/mon/profil/", name="monprofil_afficher")
     */
    public function afficher( Request $request,ParticipantRepository $participantRepository,EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
            $participant=$this->getUser();

            return $this->render('mon_profil/details.html.twig',[

                "pseudo"=>$participant->getPseudo(),
                "prenom"=>$participant->getPrenom(),
                "nom"=>$participant->getNom(),
                "telephone"=>$participant->getTelephone(),
                "email"=>$participant->getEmail(),
                "campus"=>$participant->getCampus()->getNom()
            ]);
        }


}
