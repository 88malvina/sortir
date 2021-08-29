<?php

namespace App\Controller\Api;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route ("/api")
 */
class ApiVilleController extends AbstractController

{
/**
 * @Route ("/ville", name="api_ville", methods={"GET"})
 */
public function villes(VilleRepository $villeRepository, SerializerInterface $serializer)
{

    $villes=$villeRepository->findAll();
    $json = $serializer->serialize($villes,'json',['groups'=>"list_villes"]);
    return new JsonResponse($json,Response::HTTP_OK,[],true);
}
}