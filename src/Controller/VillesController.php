<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\AjouterVilleType;
use App\Form\FiltreVilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VillesController extends AbstractController
{
/**
 * @Route ("/villes",name="villes_gerer")
 */

public function gerer(Request $request, VilleRepository $villeRepository, EntityManagerInterface $em)
{
    $ville = new Ville();
    $chercherVilleForm = $this->createForm(FiltreVilleType::class, $ville);
    $chercherVilleForm->handleRequest($request);

    $ajouterVilleForm = $this->createForm(AjouterVilleType::class, $ville);
    $ajouterVilleForm->handleRequest($request);

    $villes = $villeRepository->findAll();
    if ($ajouterVilleForm->isSubmitted() && $ajouterVilleForm->isValid()) {

        $em->persist($ville);
        $em->flush();

        $this->addFlash('success', 'La ville à été ajouter!');
        return $this->redirectToRoute('villes_gerer');

    }
    if($chercherVilleForm->isSubmitted() && $chercherVilleForm->isValid()){
        if(!empty($chercherVilleForm->get('nomSearch')->getData())){
            $villes =$villeRepository->findByNom($chercherVilleForm->get('nomSearch')->getData());
//            dd($villes);
        }
        else{
            $villes=$villeRepository->findAll();
        }


        return $this->render('sortie/villes.html.twig', [

            'ajouterVilleForm' => $ajouterVilleForm->createView(),
            'villes' => $villes,
            'chercherVilleForm' => $chercherVilleForm->createView(),
            ]);
    }

    return $this->render('sortie/villes.html.twig', [

        'ajouterVilleForm' => $ajouterVilleForm->createView(),
        'chercherVilleForm' => $chercherVilleForm->createView(),
        'villes' => $villes
    ]);

}

}