<?php

namespace App\Controller;
use App\Form\FiltrerSortiesType;
use App\Modele\SortieSearch;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request,
                          SortieRepository $sortieRepository,
                          ParticipantRepository $participantRepository): Response
    {
        //---------------------- Gestion du formulaire --------------
        //creation d'une instance de notre entite sortieSearch, qui est une classe indé dans Modele
        $sortieRecherche = new SortieSearch();
        $participant = $participantRepository->findByMail($this->getUser()->getUsername());

        //On va ajouter un setter pour injecter valeur défaut user
        $sortieRecherche->setUser($participant);

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
            "sorties" => $sorties,
            "sortieForm" => $sortieForm->createView(),
        ]);

    }

}
