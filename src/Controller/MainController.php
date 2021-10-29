<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Repository\SortieRepository;
use App\Utils\RechercheSortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     */
    public function index(SortieRepository $sortieRepository, Request $request): Response
    {
        $rechercheSortie = new RechercheSortie();
        $sortieForm = $this->createForm(SearchFormType::class, $rechercheSortie, [
            //définis la méthode GET pour ce formulaire
            'method' => 'GET',
            //régénère l'url en cas de nouvelle soumission
            'action' => $this->generateUrl('main_index')
        ]);
        $sortieForm->handleRequest($request);

        //on passe à la méthde findHouses de notre repository l'objet $searchHouse, et la page
        //$results = $houseRepository->findHouses($searchHouse, $page);

        //on passe à la méthode trouverSorties de notre SortieRepository l'objet $rechercheSortie
        $results = $sortieRepository->trouverSorties($rechercheSortie);

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sorties' => $results['sorties'],
            'form' => $sortieForm->createView()
        ]);
    }
}