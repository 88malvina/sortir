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
            ->createQueryBuilder('s');

        //pour chercher un bout de nom on laisse les % donc ok
        if (!empty($sortieSearch->getNom())) {
            $query = $query
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$sortieSearch->getNom()}%");
        }

        //pour tous les autres on injecte la valeur en dur direct sans guillemet
        if (!empty($campus)) {
            $query = $query
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $sortieSearch->getCampus());
        }

        if (!empty($dateHeureDebut)) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', $sortieSearch->getDateHeureDebut());
        }

        if (!empty($dateLimiteInscription)) {
            $query = $query
                ->andWhere('s.dateLimiteInscription <= dateLimiteInscription')
                ->setParameter('dateLimiteInscription', $sortieSearch->getDateLimiteInscription());
        }

        if (!empty($jeSuisOrganisateur)) {
            $query = $query
                ->andWhere('s.jeSuisOrganisateur = :jeSuisOrganisateur')
                ->setParameter('jeSuisOrganisateur', $sortieSearch->getJeSuisOrganisateur());
        }

        //le member ok marche uniquement car c'est du many to many
        // estce que l'objet fourni fait partie de la colllection
        if (!empty($jeSuisInscrit)) {
            $query = $query
                ->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $sortieSearch->getUser());
        }

        //On considère que l'état 5 est "passé"
        if (!empty($sortiePassee)) {
            $query = $query
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 5);
        }

        return $query->getQuery()->getResult();

        //todo Il faudra toujours ajouter un and where pour s'assurer que la date au dela de 1 mois ne s'affiche pas


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
