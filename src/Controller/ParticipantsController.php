<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
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
}