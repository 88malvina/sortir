<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participants", name="participants_")
 */
class ParticipantsController extends AbstractController
{
    /**
     * @Route("/liste", name="afficher")
     */
    public function afficherLaListeDesParticipants(ParticipantRepository $participantRepository){
        $participant=new Participant();
        $participants=$this->getDoctrine()->getRepository(Participant::class)->findAll();
//        $participants=$this->$participantRepository->findAll();
        return $this->render('participants/list.html.twig',[
                'participants'=>$participants
        ]);
    }
    /**
     * @Route("/liste/desactiver/{id}", name="desactiver")
     */
    public function DesactiverParticipants(int $id,ParticipantRepository $participantRepository,EntityManagerInterface $em){
        $participant=$this->getDoctrine()->getRepository(Participant::class)->findById($id);
        $actif=0;
        $participant->setActif($actif);
        $em->persist($participant);
        $em->flush();
        return $this->afficherLaListeDesParticipants($participantRepository);
    }
    /**
     * @Route("/liste/activer/{id}", name="activer")
     */
    public function activerParticipants(int $id,ParticipantRepository $participantRepository,EntityManagerInterface $em){
        $participant=$this->getDoctrine()->getRepository(Participant::class)->findById($id);
        $actif=1;
        $participant->setActif($actif);
        $em->persist($participant);
        $em->flush();
        return $this->afficherLaListeDesParticipants($participantRepository);
    }

    /**
     * @Route("/liste/supprimer/{id}", name="supprimer")
     */
    public function supprimerParticipants(int $id,ParticipantRepository $participantRepository,EntityManagerInterface $em){
        $participant=$this->getDoctrine()->getRepository(Participant::class)->findById($id);
        $em->remove($participant);
        $em->flush();
        return $this->afficherLaListeDesParticipants($participantRepository);;
    }
}