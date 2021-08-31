<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\CancelFormType;
use App\Form\CreateSortieType;
use App\Form\LieuType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(EntityManagerInterface $em,
                           Request $request,
                           ParticipantRepository $participantRepository
    ): Response
    {
        //ajouter le lieu
        $ajouterLieu=new Lieu();
        //le formulaire (ajouter un lieu) est appelé
        $ajouterLieuForm=$this->createForm(LieuType::class,$ajouterLieu);
        $ajouterLieuForm->handleRequest($request);

        if($ajouterLieuForm->isSubmitted() && $ajouterLieuForm->isValid()) {


            $em->persist($ajouterLieu);
            $em->flush();

            $this->addFlash('success','Le lien à été ajouter!');
            return $this->redirectToRoute('sortie_create');
        }


      //créer sortie
        $sortie=new Sortie();
        $user = $this->getUser();
        $etat = $this->getDoctrine()->getRepository(Etat::class)->find('1');
        $sortieCreationForm = $this->createForm(CreateSortieType::class, $sortie);
        $sortieCreationForm->handleRequest($request);
        if($sortieCreationForm->isSubmitted() && $sortieCreationForm->isValid()){
            $nom=$sortieCreationForm->get('nom')->getData();
            $dateSortie=$sortieCreationForm->get('dateHeureDebut')->getData();
            $dateLimite=$sortieCreationForm->get('dateLimiteInscription')->getData();
            $nbPlace=$sortieCreationForm->get('nbInscriptionMax')->getData();
            $duree=$sortieCreationForm->get('duree')->getData();
            $info=$sortieCreationForm->get('infosSortie')->getData();
            $campus=$user->getCampus();
            $villeForm=$sortieCreationForm->get('ville')->getData();
            $lieu = $this->getDoctrine()->getRepository(Lieu::class)->find($villeForm->getId());
//            $rue=$sortieCreationForm->get('rue')->getData();
//            $cp=$sortieCreationForm->get('cp')+->getData();
//            $latitude=$sortieCreationForm->get('latitude')->getData();
//            $longitude=$sortieCreationForm->get('longitude')->getData();
            $idOrganisateur= $this->getDoctrine()->getRepository(Participant::class)->find($user);


            $sortie->setNom($nom);
            $sortie->setDateHeureDebut($dateSortie);
            $sortie->setDateLimiteInscription($dateLimite);
            $sortie->setNbInscriptionMax($nbPlace);
            $sortie->setDuree($duree);
            $sortie->setInfosSortie($info);
            $sortie->setCampus($campus);
            $sortie->setOrganisateur($idOrganisateur);
            $sortie->setEtat($etat);
            $sortie->setLieu($lieu);

            //L'organisateur de la sortie est par défaut affecté en tant que participant
            $participantSortie = new Participant();
            $participantSortie = $participantRepository->findByMail($user->getUsername());
            $sortie->addParticipant($participantSortie);

            $em->persist($sortie);

            $em->flush();

            $this->addFlash('success',"La sortie a été créée!");
            return $this->redirectToRoute('sortie_afficher',['id'=>$sortie->getId()]);

        }
        return $this->render('sortie/create.html.twig', [
            'controller_name' => 'SortieController',
            'sortieCreationForm'=>$sortieCreationForm->createView(),
            'ajouterLieuForm'=>$ajouterLieuForm->createView()
        ]);
    }

    /**
     * @Route ("/ajouter", name="ajouter")
     */
    public function ajouterLieu(){
        return $this->render('sortie/ajouterLieu.html.twig');
    }

    /**
     * @Route("/inscrire/{id}", name="inscrire")
     */
    public function inscrire (int $id,
                              EntityManagerInterface $entityManager,
                              UserInterface $user,
                              ParticipantRepository $participantRepository,
                              SortieRepository $sortieRepository): Response
    {

        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        if ($sortie->getDateLimiteInscription() < new DateTime())
        {
            $this->addFlash('notice', 'La date limite de inscription à la sortie '.$sortie->getNom().
                ' est passé, désolé ! :(');
        } else
        {
            $sortie->addParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes désormais inscrit à la sortie '.$sortie->getNom().' !');
        }

        return $this->render( 'sortie/inscrire.html.twig' , [
            'sortie' => $sortie
        ]);
    }


    /**
     * @Route("/desister/{id}", name="desister")
     */
    public function desister (int $id,
                              EntityManagerInterface $entityManager,
                              UserInterface $user,
                              ParticipantRepository $participantRepository,
                              SortieRepository $sortieRepository): Response
    {

        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        $sortie->removeParticipant($participant);
        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash('success', "Vous n'êtes plus inscrit à la sortie ".$sortie->getNom().' !');

        return $this->render( 'sortie/inscrire.html.twig' , [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/annulerSortie/{id}", name="annuler")
     */
    public function annuler (int $id,
                              EtatRepository $etatRepository,
                              EntityManagerInterface $entityManager,
                              UserInterface $user,
                              ParticipantRepository $participantRepository,
                              SortieRepository $sortieRepository,
                              Request $request
    ): Response

    {

        //On commence par gérer une instance de formulaire d'annulation
        $cancelForm = $this->createForm(CancelFormType::class);
        $cancelForm->handleRequest($request);

        //On veut que l'organisateur puisse annuler la sortie si celle-ci est bien annulable
        //Depuis le bouton dans la liste des sorties

        //d'abord on vérifie que la sortie est annulable, c'est à dire qu'elle n'est pas passée et que l'on est bien
        //l'organisateur.
        // todo Gérer ceci avec voter plus tard ?

        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        //Si le formulaire cancelForm est ok on passe à l'action consistant à changer l'état de la sortie et sa description
        //Nouvel état = annulé, donc ne s'affichera plus
        //Nouvelle description = intègre motif d'annulation qui servira juste à l'archivage en base

        if ($cancelForm->isSubmitted() && $cancelForm->isValid()){
            //On dit que l'état 2 correspond à annuler
            $etat = new Etat();
            $etat = $etatRepository->find(2);

            $descriptionInitiale = $sortie->getInfosSortie();
            $motif = $cancelForm['motif']->getData();

            $nouveauDescription = strval($descriptionInitiale) . strval($motif);

            //0n modifie les infos de la sortie avec le motif d'annulation
            $sortie->setInfosSortie($nouveauDescription);

            //On modifie l'état de la sortie en annulé
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', "La sortie a été annulée ".$sortie->getNom().' !');

            return $this->redirectToRoute('main_home');
        }

        //On lui passe le cancelForm

        return $this->render( 'sortie/annuler.html.twig' , [
            "cancelForm" => $cancelForm->createView(),
            "sortie" => $sortie
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier (int $id,
                             EtatRepository $etatRepository,
                             EntityManagerInterface $em,
                             UserInterface $user,
                             ParticipantRepository $participantRepository,
                             SortieRepository $sortieRepository,
                             LieuRepository $lieuRepository,
                             Request $request
    ): Response

        //Donc on veut que l'organisateur soit redirigé vers une page modification avec le formulaire
        // tous les champs renseignés et possibilité de modifier.html.twig
        //Pour ça on va copier la page créer sortie et rebalancer les données dans les champs
        //Il faudra vérifier que l'on est bien le créateur de la sortie d'abord
    {

        //On commence par créer une nouvelle sortie qui va permettre de réinjecter les données
        $sortie = new Sortie();
        //On hydrate la sortie avec les données de la sortie sur laquelle on est
        $sortie = $sortieRepository->findById($id);
        //On récupère l'user qui sera utile pour la verif à suivre
        $user = $this->getUser();

        //----------------- gestion du formulaire de lieu-----------
        //D'abord on veut récupérer les infos du lieu de la sortie à modifier
        $modifierLieu = new Lieu();
        //On hydrate le lieu avec le lieu renseigné avant
        $modifierLieu = $lieuRepository->find($sortie->getLieu()->getId());
        $ajouterLieuForm = $this->createForm(LieuType::class,$modifierLieu);
        //----------------------------------------------------------

        //On créé un formulaire de type create Form
        $sortieModifierForm = $this->createForm(CreateSortieType::class,$sortie);
        //On oublie pas de faire le handleRequest
        $sortieModifierForm->handleRequest($request);

        //Vérification que la personne qui modifie est bien l'organisateur
        if($user != $sortie->getOrganisateur()) {
            $this->addFlash('fail', 'Vous ne pouvez pas modifier cette sortie');
        }
        //si le formulaire a été submit, on le persist en base
        //todo corriger ça xD
        elseif ($sortieModifierForm->isSubmitted() && $sortieModifierForm->isValid()) {
            //On récupère les champs (2 façons de l'écrire pour info)
            $nom = $sortieModifierForm['nom']->getData();
            $dateSortie = $sortieModifierForm->get('dateHeureDebut')->getData();
            $dateLimite=$sortieModifierForm->get('dateLimiteInscription')->getData();
            $nbPlace=$sortieModifierForm->get('nbInscriptionMax')->getData();
            $duree=$sortieModifierForm->get('duree')->getData();
            $info=$sortieModifierForm->get('infosSortie')->getData();
            $campus=$user->getCampus();
            $villeForm=$sortieModifierForm->get('ville')->getData();
            $lieu = $this->getDoctrine()->getRepository(Lieu::class)->find($villeForm->getId());

            //L'état que l'on veut attribuer est l'état 1 que l'on va instancier
            $etat = $this->getDoctrine()->getRepository(Etat::class)->find('1');

            $sortie->setNom($nom);
            $sortie->setDateHeureDebut($dateSortie);
            $sortie->setDateLimiteInscription($dateLimite);
            $sortie->setNbInscriptionMax($nbPlace);
            $sortie->setDuree($duree);
            $sortie->setInfosSortie($info);
            $sortie->setCampus($campus);
            $sortie->setEtat($etat);
            $sortie->setLieu($lieu);

            $em->persist($sortie);
            $em->flush();

            //On met un petit message de success
            $this->addFlash('success', 'Sortie modifiée :D');
        }

        //On dirige vers la page modifier.html.twig en passant la sortie hydratée

        return $this->render('sortie/modifier.html.twig', [
            'sortieModifierForm'=>$sortieModifierForm->createView(),
            'ajouterLieuForm'=>$ajouterLieuForm->createView()
        ]);
    }




    /**
     * @param Request $request
     * @return JsonResponse
     */



    /**
     * @Route("/{id}", name="afficher")
     */
    public function afficher(int $id,
                             SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->findById($id);

        return $this->render('sortie/details.html.twig',[
            "sortie"=>$sortie
        ]);
    }


}
