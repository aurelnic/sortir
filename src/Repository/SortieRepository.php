<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Utils\RechercheSortie;
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

    public function trouverSorties($sortie){

        $queryBuilder = $this->createQueryBuilder('h');
        $queryBuilder->addOrderBy('h.dateSortie', 'ASC');

        /**
         * @var RechercheSortie $sortie
         */
        //conditionnement de ma requête,
        //selon site choisi
        if($sortie->getSite()){
            $queryBuilder->andWhere('h.site >= :site');
            $queryBuilder->setParameter('site', $sortie->getSite());
        }

        // si recherce par mot
        if($sortie->getRecherche()){
            $queryBuilder->andWhere('h.recherche LIKE :recherche');
            $queryBuilder->setParameter('recherche', '%'.$sortie->getRecherche().'%');
        }

        //entre date de début
        if($sortie->getDateDebut()){
            $queryBuilder->andWhere('h.dateSortie >= :dateDebut');
            $queryBuilder->setParameter('dateDebut', $sortie->getDateDebut());
        }

        //et date de fin
        if($sortie->getDateFin()){
            $queryBuilder->andWhere('h.dateSortie <= :dateFin');
            $queryBuilder->setParameter('dateFin', $sortie->getDateFin());
        }

        //si organisateur
        if($sortie->getOrganisateur()){
            $queryBuilder->andWhere('h.organisateur = :organisateur');
            $queryBuilder->setParameter('organisateur', $sortie->getOrganisateur());
        }

        //si inscrit
        if($sortie->getInscrit()){
            $queryBuilder->andWhere('h.inscrit = :inscrit');
            $queryBuilder->setParameter('inscrit', $sortie->getInscrit());
        }

        //si pas inscrit
        if($sortie->getPasInscrit()){
            $queryBuilder->andWhere('h.pasInscrit = :pasInscrit');
            $queryBuilder->setParameter('pasInscrit', $sortie->getPasInscrit());
        }

        //si sorties passees
        if($sortie->getSortiesPassees()){
            $queryBuilder->andWhere('h.sortiesPassees = :sortiesPassees');
            $queryBuilder->setParameter('sortiesPassees', $sortie->getSortiesPassees());
        }

        //doit refaire le select pour bien récupérer les résultats
        $queryBuilder->select('h');
        $query = $queryBuilder->getQuery();

        return [
            'sorties' => $query->getResult()
        ];
    }
}
