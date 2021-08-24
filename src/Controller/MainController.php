<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(SortieRepository $sortieRepository)
    {
        //---------------------- Gestion du formulaire --------------
        //creation d'une instance de notre entite sortie
        $sortieRecherche = new Sortie();
        //creation d'une instance du formulaire
        $sortieForm = $this->createForm(FiltrerSortiesType::class, $sortieRecherche);

        //On pense à faire le handle request, pour cela on pense à passer la request en argument de fonction
        //$sortieForm->handleRequest()

        //todo traiter le formulaire

        //---------------------- Gestion affichage sorties --------------
        $sortie = $sortieRepository->findBy([],['dateLimiteInscription' => 'DESC']);

        //Pour l'affichage de toutes les sorties, on passe au twig la liste des sorties
        //On passe aussi le formulaire (sans oublier le create view)

        return $this->render('main/index.html.twig',[
            "sortie" => $sortie,
            "sortieForm" => $sortieForm->createView(),
        ]);

    }



}
