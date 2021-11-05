<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

    /**
     * @Route("/gestion/villes", name="gestion_villes")
     */
    public function liste(VilleRepository $villeRepository, Request $request) : Response
    {
        //Recherche de la liste des villes + filtrage si présent
        $villeForm =$this->createFormBuilder()
            ->add('nom',TextType::class, ['required' => false])
            ->getForm();
        $villeForm->handleRequest($request);

        //Champs pour l'ajout de villes
        $ajoutVille = new Ville();
        $villeAjoutForm = $this->createForm(VilleType::class, $ajoutVille);
        $villeAjoutForm->handleRequest($request);


        //Evaluation formulaires
        if ($villeAjoutForm->isSubmitted() && $villeAjoutForm->isValid())
        {
            $this->add($ajoutVille);
        }

        if ($villeForm->isSubmitted() && $villeForm->isValid())
        {
            $data = $villeForm->getData();
            $villes = $villeRepository->findVilleLike($data['nom']);
        }
        else
        {
            $villes = $villeRepository->findAll();
        }

        return $this->render('gestion/villes.html.twig', [
            'villes' => $villes,
            'filtreForm' => $villeForm->createView(),
            'villeajoutform' => $villeAjoutForm->createView()

        ]);
    }


    public function add(Ville $ville) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ville);
        $entityManager->flush();

        return $this->redirectToRoute('gestion_villes');
    }

    /**
     * @Route("/gestion/villes/delete/{id}", name="gestion_villes_delete")
     */
    public function delete($id, EntityManagerInterface $entityManager, VilleRepository $villeRepository, Request $request) : Response
    {
        $ville = $villeRepository->findOneById($id);

        if($ville)
        {
            $entityManager->remove($ville);
            $entityManager->flush();
            $this->addFlash('success',"Ville supprimée avec succès!");
        }
        else
        {
            $this->addFlash('error',"Ville non trouvée, Veuillez réessayer !");
        }


        return $this->redirectToRoute('gestion_villes');
    }

    /**
     * @Route("/gestion/villes/edit/{id}", name="gestion_villes_edit")
     */
    public function edit($id, EntityManagerInterface $entityManager, VilleRepository $villeRepository, Request $request) :Response
    {
        $ville = $villeRepository->findOneById($id);

        $editForm = $this->createForm(VilleType::class, $ville);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('gestion_villes');
        }


        return $this->render('gestion/villes/edit.html.twig', [
            'ville' => $ville,
            'editForm' => $editForm->createView()

        ]);
    }

}
