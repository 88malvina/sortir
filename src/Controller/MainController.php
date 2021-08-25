<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltrerSortiesType;
use App\Modele\SortieSearch;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request, SortieRepository $sortieRepository)
    {
        //---------------------- Gestion du formulaire --------------
        //creation d'une instance de notre entite sortie
        $sortieRecherche = new SortieSearch();

        //On va ajouter un setter pour injecter valeur défaut user
        $sortieRecherche->setUser($this->getUser());

        //creation d'une instance du formulaire
        $sortieForm = $this->createForm(FiltrerSortiesType::class, $sortieRecherche);

        //On pense à faire le handle request, pour cela on pense à passer la request en argument de fonction
        dump($sortieForm);
        $sortieForm->handleRequest($request);
        dump($sortieForm);

        $sorties = $sortieRepository->findByParameters($sortieRecherche);


        //Pour l'affichage de toutes les sorties, on passe au twig la liste des sorties
        //On passe aussi le formulaire (sans oublier le create view)

        return $this->render('main/index.html.twig',[
            "sortie" => $sorties,
            "sortieForm" => $sortieForm->createView(),
        ]);

    }



}
