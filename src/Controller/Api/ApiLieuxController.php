<?php

namespace App\Controller\Api;
use App\Entity\Lieu;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route ("/api/lieux")
 */
class ApiLieuxController extends AbstractController
{
/**
 * @Route("/list_lieux")
 */
    public function listOfLieux(LieuRepository $lieuRepository,SerializerInterface $serializer,Request $request){
        $lieux=$lieuRepository->listOfLieuxByVille($request);
        $json = $serializer->serialize($lieux,'json',['groups'=>"list_lieux"]);
        return new JsonResponse($json,Response::HTTP_OK,[],true);
    }

}