<?php

namespace App\Controller;

use App\Entity\Ville;
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
    $filtrerVilleForm = $this->createForm(FiltreVilleType::class, $ville);
    $filtrerVilleForm->handleRequest($request);

    $villes = $villeRepository->findAll();

    if($filtrerVilleForm->isSubmitted() && $filtrerVilleForm->isValid()){

        $em->persist($ville);
        $em->flush();
        return $this->redirectToRoute('villes_gerer');

    }

    return $this->render('sortie/villes.html.twig', [
        'filtrerVilleForm' => $filtrerVilleForm->createView(),
        'villes' => $villes
    ]);
}
/**
 * @Route ("/villes",name="villes_ajoute")
 */
public function ajouter(VilleRepository $villeRepository, EntityManagerInterface $em){

    return $this->render('sortie/villes.html.twig', [

    ]);
}
}