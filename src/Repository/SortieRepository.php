<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Modele\SortieSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    //Il nous faut une fonction spécifique pour renvoyer les sorties après filtre
    public function findByParameters(SortieSearch $sortieSearch)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->addSelect('e')
            ->join('s.organisateur', 'o')
            ->addSelect('o')
            ->join('s.participants', 'p')
            ->addSelect('p');


        //todo Il faudra toujours ajouter un and where pour s'assurer que la date au dela de 1 mois ne s'affiche pas

        //On commence par récupérer une variable qui retourne le jour du mois dernier
        $lastmonth = date('Y-m-d', strtotime('-1month'));
        dump("lastmonth");
        dump($lastmonth);
        dump("heure debut");
        dump($sortieSearch->getDateHeureDebut());

        //on veut virer les vieilles sorties pourries par défaut (sans utiliser le form) donc on a besoin de la date
        //des sorties
        $query = $query
            ->andWhere('s.dateHeureDebut > :moisDernier')
            ->setParameter('moisDernier', $lastmonth);

        //On ne veut pas afficher les sorties qui ont été annulées
        $query = $query
            ->andWhere('s.etat <> 2 ');

        //pour chercher un bout de nom on laisse les % donc ok
        if (!empty($sortieSearch->getNom())) {
            $query = $query
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$sortieSearch->getNom()}%");
        }

        //pour tous les autres on injecte la valeur en dur direct sans guillemet
        if (!empty($sortieSearch->getCampus())) {
            $query = $query
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $sortieSearch->getCampus());
        }

        if (!empty($sortieSearch->getDateHeureDebut())) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', $sortieSearch->getDateHeureDebut());
        }

        if (!empty($sortieSearch->getDateLimiteInscription())) {
            $query = $query
                ->andWhere('s.dateLimiteInscription <= :dateLimiteInscription')
                ->setParameter('dateLimiteInscription', $sortieSearch->getDateLimiteInscription());
        }

        if (!empty($sortieSearch->getJeSuisOrganisateur())) {
            $query = $query
                ->andWhere('s.organisateur = :jeSuisOrganisateur')
                ->setParameter('jeSuisOrganisateur', $sortieSearch->getUser()->getNom());
        }

        //le member ok marche uniquement car c'est du many to many
        // il se demande en gros est ce que l'objet fourni fait partie de la collection
        if (!empty($sortieSearch->getJeSuisInscrit())) {
            $query = $query
                ->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $sortieSearch->getUser());
        }

        //le member ok marche uniquement car c'est du many to many
        // il se demande en gros est ce que l'objet fourni fait partie de la collection
        if (!empty($sortieSearch->getJeNeSuisPasInscrit())) {
            $query = $query
                ->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $sortieSearch->getUser());
        }


        //On considère que l'état 5 est "passé"
        if (!empty($sortieSearch->getSortiePassee())) {
            $query = $query
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 5);
        }

        return $query->getQuery()->getResult();




    }


    /**
     * @return Sortie Returns a Sortie object
     */
    public function findById($id) : Sortie
    {
        $sortie = new Sortie();

        $array = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        foreach ($array as $s){
            $sortie = $s;
        }

        return $sortie;
    }



    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
