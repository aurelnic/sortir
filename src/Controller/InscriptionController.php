<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription/{id_sortie}", name="inscription_inscription")
     */
    public function inscription($id_sortie, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->findOneById($id_sortie);
        $user = $this->getUser();


        //Si le nombre max de participant n'est pas atteint,
        if(count($sortie->getListeParticipants()) >= $sortie->getNbreParticipants())
        {
            $this->addFlash('error',"Cette sortie est déjà complète !");
            return $this->redirectToRoute('main_index');
        }

        // si la sortie est publiée et que la date de cloture n'est pas encore passée
        if($sortie->getEtat() == 1 && $sortie->getDateCloture()>= new \DateTime('now'))
        {
            //(Vérification supplémentaire) si le participant n'est pas déjà dans la liste des inscrits
            if(!$sortie->getListeParticipants()->contains($user))
            {

                $sortie->addListeParticipant($user);

                //Update en base
                $entityManager->persist($sortie);
                $entityManager->flush();

                //Feedback utilisateur
                $this->addFlash('success',"Inscription enregistrée !");

            }
            else
            {
                $this->addFlash('error',"Vous êtes déjà inscrit!");
            }
        }
        else
        {
            $this->addFlash('error',"Impossible de faire l'inscription!");
        }

        return $this->redirectToRoute('main_index');
    }

    /**
     * @Route("/desister/{id_sortie}", name="inscription_desister")
     */
    public function desister($id_sortie, SortieRepository $sortieRepository, EntityManagerInterface $entityManager) : Response
    {
        $sortie = $sortieRepository->findOneById($id_sortie);
        $user = $this->getUser();

        //Si la date de la sortie n'est pas encore passée
        if($sortie->getDateSortie()>= new \DateTime('now'))
        {
            if($sortie->getListeParticipants()->contains($user))
            {
                $sortie->removeListeParticipant($user);
                //$user->getSorties()->remove($sortie);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success',"Désistement enregistrée !");
            }
            else
            {
                $this->addFlash('error',"Vous n'êtes pas inscrit à cette sortie!");
            }

        }

        return $this->redirectToRoute('main_index');
    }
}
