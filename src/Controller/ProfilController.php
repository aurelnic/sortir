<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfilController extends AbstractController
{
    /**
     * @Route("/creationProfil", name="app_creation_profil")
     */
    public function creationProfil(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, SiteRepository $SiteRepository): Response
    {
        $user = new Participant();
        $sites = $SiteRepository->findAll();
        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('main');
        }

        return $this->render('profil/creationProfil.html.twig', [
            'profilForm' => $form->createView(),
            "sites"=>$sites
        ]);
    }

    /**
     * @Route("/editProfil/{id}", name="app_edit_profil", requirements={"id" = "\d+"})
     */
    public function editProfil($id, ParticipantRepository $participantRepository, Request $request, Security $security): Response
    {
        $user = $participantRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException("Désolé, cet utilisateur n'existe pas !");
        }
        if ($user != $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce profil');
            return $this->redirectToRoute('main');
        }

        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce modifiée !');
            return $this->redirectToRoute('app_detail_profil', ['id' => $user->getId()]);
        }

        return $this->render('profil/creationProfil.html.twig', [
            'profilForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/detailProfil/{id}", name="app_detail_profil", requirements={"id" = "\d+"})
     */
    public function detailProfil($id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        if(!$participant){
            throw $this->createNotFoundException("Le participant recherché n'existe pas");
        }

        return $this->render('profil/detailProfil.html.twig', [
            'participant' => $participant
        ]);
    }
}