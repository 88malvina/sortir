<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(SortieRepository $sortieRepository)
    {

        //On passe au twig la liste des sorties
        $sortie = $sortieRepository->findBy([],['dateLimiteInscription' => 'DESC']);

        return $this->render('main/index.html.twig',[
            "sortie" => $sortie
        ]);

    }

}
