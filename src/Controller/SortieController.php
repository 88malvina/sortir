<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
