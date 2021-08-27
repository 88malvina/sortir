<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateSortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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






}
