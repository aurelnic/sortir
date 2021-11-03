<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route("/api/get-users", name="app_get_users")
     */
    public function getUsers(): Response
    {
        return $this->json('{"prenom":"adel"}');
    }


    /**
     * @Route("/main", name="main")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
