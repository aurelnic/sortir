<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
//    /**
//     * @Route("/sortie", name="sortie")
//     */
//    public function index(): Response
//    {
//        return $this->render('sortie/index.html.twig', [
//            'controller_name' => 'SortieController',
//        ]);
//    }
    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository): Response{
        $sortie = $sortieRepository->find($id);
        if(!$sortie) {
            throw $this->createNotFoundException("Sortie Inconnue!");
        }

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }
    /**
     * @Route("/sortie/create/{id}", name="sortie_create")
     */
    public function create($id, ParticipantRepository $participantRepository, SiteRepository $siteRepository, VilleRepository $villeRepository,LieuRepository $lieuRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $organisateur = $participantRepository->find($id);
        $site = $siteRepository->find($organisateur->getRattachement());
        $villes = $villeRepository->findAll();
        $lieux = $lieuRepository->findAll();
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setEtat(1);
            $sortie->setOrganisateur($organisateur);
            $sortie->setSite($site);
            $entityManager->persist($sortie);
            $entityManager->flush();
            // Ajouter message de validation
            // Ajouter la redirection
            // return $this->redirectToRoute();
        }
        return $this->render('sortie/create.html.twig', [
            'site' => $site,
            'lieux' => $lieux,
            'villes' => $villes,
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/get-lieux/{ville_id}", name="app_get_lieux")
     */
    public function getLieux(LieuRepository $lieuRepository,$ville_id): Response
    {
        $lieux=$lieuRepository->findBy(["ville"=>$ville_id]);

        $json = '[';
        foreach($lieux as $lieu){
            $json .='{"nomLieu":"'.$lieu->getNomLieu().'","id":"'.$lieu->getId().'"},';
        }
        $json = substr($json,0, -1);
        $json .=']';

        return $this->json($json);
    }

    /**
     * @Route("/get-info-lieux/{lieu_id}", name="app_get_info_lieux")
     */
    public function getInfoLieux(LieuRepository $lieuRepository,$lieu_id): Response
    {
        $lieu=$lieuRepository->find($lieu_id);
        $table = ["adresse"=>$lieu->getAdresse(), "longitude"=>$lieu->getLongitudeGPS(), "latitude"=>$lieu->getLatitudeGPS()];

        return $this->json(json_encode($table));
    }
}
