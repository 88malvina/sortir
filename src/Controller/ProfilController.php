<?php

namespace App\Controller;

use App\Form\MonProfilFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/profil", name="profil_")
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/{id}", name="afficher", requirements={"id" = "\d+"})
     */
    public function afficher(int $id,
                             ParticipantRepository $participantRepository): Response
    {
            $participant = $participantRepository->findById($id);

            return $this->render('profil/details.html.twig',[
                "participant"=>$participant
            ]);
    }

    /**
     * @Route("/modifier", name="modifier")
     */
    public function modifier( Request $request,
                              UserPasswordEncoderInterface $userPassword,
                              SluggerInterface $slugger,
                              EntityManagerInterface $em): Response
    {
        $participant = $this->getUser();
        $profilForm = $this->createForm(MonProfilFormType::class, $participant);

        $profilForm->handleRequest($request);
        if($profilForm->isSubmitted() && $profilForm->isValid()) {
            $uploadedFile = $profilForm->get('image')->getData();
            $pseudo = $profilForm->get('pseudo')->getData();
            $prenom = $profilForm->get('prenom')->getData();dump($prenom);
            $nom = $profilForm->get('nom')->getData();
            $telephone = $profilForm->get('telephone')->getData();
            $email = $profilForm->get('email')->getData();
            $password = $profilForm->get('password')->getData();
            $newUncodPassword = $userPassword->encodePassword($participant,$password);
            $campus = $profilForm->get('campus')->getData();

            if ($uploadedFile) {

                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename);
                $participant->setImage($newFilename);

                $em->flush();
            }
            $participant->setPseudo($pseudo);
            $participant->setPrenom($prenom);
            $participant->setNom($nom);
            $participant->setTelephone($telephone);
            $participant->setEmail($email);
            $participant->setPassword($newUncodPassword);
            $participant->setCampus($campus);
            $em->persist($participant);
            $em->flush();

            $this->addFlash('success', 'Votre profil a été modifié.');
        }
        return $this->render('profil/monProfil.html.twig',[
            "avatar"=>$participant->getImage(),
            "profilForm"=>$profilForm->createView()
        ]);
    }

}
