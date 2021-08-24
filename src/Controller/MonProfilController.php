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
    public function afficher( Request $request): Response
    {
            $participant=$this->getUser();

            return $this->render('mon_profil/details.html.twig',[

                "pseudo"=>$participant->getPseudo(),
                "prenom"=>$participant->getPrenom(),
                "nom"=>$participant->getNom(),
                "telephone"=>$participant->getTelephone(),
                "email"=>$participant->getEmail(),
                "campus"=>$participant->getCampus()->getNom(),
                "avatar"=>$participant->getImage()
            ]);
        }

    /**
     * @Route("/mon/profil/modifier", name="monprofil_modifier")
     */
    public function modifier( Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $participant=$this->getUser();
        $profilForm = $this->createForm(MonProfilFormType::class, $participant);

        $profilForm->handleRequest($request);
        if($profilForm->isSubmitted() && $profilForm->isValid()) {
            $uploadedFile = $profilForm->get('image')->getData();

            if ($uploadedFile) {

                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename);

                $participant->setImage($newFilename);
                $em->flush();
            }

        }
        return $this->render('mon_profil/monProfil.html.twig',[
            "avatar"=>$participant->getImage(),
            "profilForm"=>$profilForm->createView()
        ]);
    }

}
