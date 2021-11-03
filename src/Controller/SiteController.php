<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Ville;
use App\Form\SiteType;
use App\Form\VilleType;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{

    /**
     * @Route("/gestion/sites", name="gestion_sites")
     */
    public function liste(SiteRepository $siteRepository, Request $request) : Response
    {
        //Recherche de la liste des sites + filtrage si présent
        $filtreForm =$this->createFormBuilder()
            ->add('nom',TextType::class, ['required' => false])
            ->getForm();
        $filtreForm->handleRequest($request);

        //Champs pour l'ajout de sites
        $site = new Site();
        $ajoutForm = $this->createForm(SiteType::class, $site);
        $ajoutForm->handleRequest($request);


        //Evaluation formulaires
        if ($ajoutForm->isSubmitted() && $ajoutForm->isValid())
        {
            $this->add($site);
        }

        if ($filtreForm->isSubmitted() && $filtreForm->isValid())
        {
            $data = $filtreForm->getData();
            $sites = $siteRepository->findSiteLike($data['nom']);
        }
        else
        {
            $sites = $siteRepository->findAll();
        }

        return $this->render('gestion/sites.html.twig', [
            'sites' => $sites,
            'filtreForm' => $filtreForm->createView(),
            'ajoutForm' => $ajoutForm->createView()

        ]);
    }


    public function add(Site $site) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($site);
        $entityManager->flush();

        return $this->redirectToRoute('gestion_sites');
    }

    /**
     * @Route("/gestion/sites/delete/{id}", name="gestion_sites_delete")
     */
    public function delete($id, EntityManagerInterface $entityManager, SiteRepository $siteRepository, Request $request) : Response
    {
        $site = $siteRepository->findOneById($id);

        if($site)
        {
            $entityManager->remove($site);
            $entityManager->flush();
            $this->addFlash('success',"Site supprimé avec succès!");
        }
        else
        {
            $this->addFlash('error',"Site non trouvé, Veuillez réessayer !");
        }


        return $this->redirectToRoute('gestion_sites');
    }

    /**
     * @Route("/gestion/sites/edit/{id}", name="gestion_sites_edit")
     */
    public function edit($id, EntityManagerInterface $entityManager, SiteRepository $siteRepository, Request $request) :Response
    {
        $site = $siteRepository->findOneById($id);

        $editForm = $this->createForm(SiteType::class, $site);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('gestion_sites');
        }


        return $this->render('gestion/sites/edit.html.twig', [
            'site' => $site,
            'editForm' => $editForm->createView()

        ]);
    }
}
