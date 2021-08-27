<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CancelFormType;
use App\Form\CreateSortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieController extends AbstractController
{
    /**
     * @Route("/creer/sortie", name="creer_sortie")
     */
    public function create(): Response
    {
        $sortie=new Sortie();
        $sortieCreationForm = $this->createForm(CreateSortieType::class, $sortie);
        return $this->render('sortie/create.html.twig', [
            'controller_name' => 'SortieController',
            'sortieCreationForm'=>$sortieCreationForm->createView()
        ]);
    }

    /**
     * @Route("/inscrire/{id}", name="inscrire")
     */
    public function inscrire (int $id,
                              EntityManagerInterface $entityManager,
                              UserInterface $user,
                              ParticipantRepository $participantRepository,
                              SortieRepository $sortieRepository): Response
    {

        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        if ($sortie->getDateLimiteInscription() < new \DateTime())
        {
            $this->addFlash('notice', 'La date limite de inscription à la sortie '.$sortie->getNom().
                ' est passé, désolé ! :(');
        } else
        {
            $sortie->addParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes désormais inscrit à la sortie '.$sortie->getNom().' !');
        }

        return $this->render( 'sortie/inscrire.html.twig' , [
            'sortie' => $sortie
        ]);
    }


    /**
     * @Route("/desister/{id}", name="desister")
     */
    public function desister (int $id,
                              EntityManagerInterface $entityManager,
                              UserInterface $user,
                              ParticipantRepository $participantRepository,
                              SortieRepository $sortieRepository): Response
    {

        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash('success', "Vous n'êtes plus inscrit à la sortie ".$sortie->getNom().' !');

        return $this->render( 'sortie/inscrire.html.twig' , [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/annulerSortie/{id}", name="Sortie_annuler")
     */
    public function annuler (int $id,
                              EtatRepository $etatRepository,
                              EntityManagerInterface $entityManager,
                              UserInterface $user,
                              ParticipantRepository $participantRepository,
                              SortieRepository $sortieRepository,
                              Request $request
    ): Response

    {

        //On commence par gérer une instance de formulaire d'annulation
        $cancelForm = $this->createForm(CancelFormType::class);
        $cancelForm->handleRequest($request);

        //On veut que l'organisateur puisse annuler la sortie si celle-ci est bien annulable
        //Depuis le bouton dans la liste des sorties

        //d'abord on vérifie que la sortie est annulable, c'est à dire qu'elle n'est pas passée et que l'on est bien
        //l'organisateur.
        // todo Gérer ceci avec voter plus tard ?

        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        //Si le formulaire cancelForm est ok on passe à l'action consistant à changer l'état de la sortie et sa description
        //Nouvel état = annulé, donc ne s'affichera plus
        //Nouvelle description = intègre motif d'annulation qui servira juste à l'archivage en base

        if ($cancelForm->isSubmitted() && $cancelForm->isValid()){
            //On dit que l'état 2 correspond à annuler
            $etat = new Etat();
            $etat = $etatRepository->find(2);

            $descriptionInitiale = $sortie->getInfosSortie();
            $motif = $cancelForm['motif']->getData();

            $nouveauDescription = strval($descriptionInitiale) . strval($motif);

            //0n modifie les infos de la sortie avec le motif d'annulation
            $sortie->setInfosSortie($nouveauDescription);

            //On modifie l'état de la sortie en annulé
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', "La sortie a été annulée ".$sortie->getNom().' !');

            return $this->redirectToRoute('main_home');
        }

        //On lui passe le cancelForm

        return $this->render( 'sortie/annuler.html.twig' , [
            "cancelForm" => $cancelForm->createView(),
            "sortie" => $sortie
        ]);
    }





}
